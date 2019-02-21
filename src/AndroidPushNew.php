<?php

namespace Chipolo\Push;
use Google_Client;

class AndroidPushNew extends BasePush
{
    private $repeated = 0;

    public function createToken()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/www/firebase_service_account_chipolo.json');
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope("https://www.googleapis.com/auth/firebase.messaging");
        $client->refreshTokenWithAssertion();

        $token = $client->getAccessToken();

        return array_key_exists('access_token', $token) ? $token['access_token'] : null;
    }

    public function send(
        string $token,
        array $payload
    ): CurlResponse {
        $response = $this->setUrl('https://fcm.googleapis.com/v1/projects/api-project-83947113929/messages:send')
            ->setPayload(array_merge_recursive($payload, [
                'message' => [
                    'token' => $token,
                ],
            ]))
            ->setHeaders([
                'Content-Type'     => 'application/json; UTF-8',
                'Authorization'    => 'Bearer ' . $this->createToken(),
            ])->handle();
                dd($response);
        if ($response->getStatusCode() != 200 && $this->repeated < 3) {
            $this->repeated++;
            $this->send($token, $payload, $topic);
        }

        $this->repeated = 0;
        return $response;
    }
}
