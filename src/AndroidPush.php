<?php

namespace Chipolo\Push;

class AndroidPush extends BasePush
{
    private $repeated = 0;

    public function send(
        string $token,
        array $payload
    ): CurlResponse {
        $response = $this->setUrl('https://fcm.googleapis.com/fcm/send')
            ->setPayload(array_merge_recursive($payload, [
                'registration_ids' => [$token],
            ]))
            ->setHeaders([
                'Content-Type'     => 'application/json',
                'Authorization'    => 'Key=' . config('chipolo-push.android.authorization-key'),
            ])->handle();

        if ($response->getStatusCode() != 200 && $this->repeated < 3) {
            $this->repeated++;
            $this->send($token, $payload, $topic);
        }

        $this->repeated = 0;
        return $response;
    }
}
