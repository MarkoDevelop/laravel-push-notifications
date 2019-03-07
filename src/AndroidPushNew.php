<?php

namespace Chipolo\Push;

use Google_Client;

class AndroidPushNew extends BasePush
{
    private $repeated = 0;

    public function createToken()
    {
        $client = new Google_Client();

        // Laravel 5.8 no longer loads .env variables in a way that would work with getenv()
        if (config('chipolo-push.android.google_application_credentials')) {
            $client->setAuthConfig(config('chipolo-push.android.google_application_credentials'));
        } else {
            $client->useApplicationDefaultCredentials();
        }

        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();

        $token = $client->getAccessToken();

        return array_key_exists('access_token', $token) ? $token['access_token'] : null;
    }

    public function send(
        string $token,
        array $payload
    ): CurlResponse {
        $projectId = config('chipolo-push.android.project_id');
        $response = $this->setUrl('https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send')
            ->setPayload(array_merge_recursive($payload, [
                'message' => [
                    'token' => $token,
                ],
            ]))
            ->setHeaders([
                'Content-Type'     => 'application/json; UTF-8',
                'Authorization'    => 'Bearer ' . $this->createToken(),
            ])->handle();

        if ($response->getStatusCode() != 200 && $this->repeated < 3) {
            $this->repeated++;
            $this->send($token, $payload);
        }

        $this->repeated = 0;
        return $response;
    }
}
