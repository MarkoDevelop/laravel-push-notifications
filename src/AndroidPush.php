<?php

namespace Overthink\Push;

use Google_Client;

class AndroidPush extends BasePush
{
    public function createToken()
    {
        $client = new Google_Client();
        // Laravel 5.8 no longer loads .env variables in a way that would work with getenv()
        if (config('overthink-push.android.google_application_credentials')) {
            $client->setAuthConfig(config('overthink-push.android.google_application_credentials'));
        } else {
            $client->useApplicationDefaultCredentials();
        }

        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();

        $accessToken = $client->getAccessToken();

        return array_key_exists('access_token', $accessToken) ? $accessToken['access_token'] : null;
    }

    public function send(
        string $token,
        array $payload
    ): CurlResponse {
        $this->setToken($token);

        $projectId = config('overthink-push.android.project_id');
        return $this->setUrl('https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send')
            ->setPayload(array_merge_recursive($payload, [
                'message' => [
                    'token' => $token,
                ],
            ]))
            ->setHeaders([
                'Content-Type'     => 'application/json; UTF-8',
                'Authorization'    => 'Bearer ' . $this->createToken(),
            ])->handle();
    }
}
