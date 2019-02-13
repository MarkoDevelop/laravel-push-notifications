<?php

namespace Chipolo\Push;

class AndroidPush extends BasePush
{
    private $repeated = 0;

    public function send(
        string $token,
        array $payload,
        string $topic
    ): CurlReponse {
        $response = $this->setUrl('https://fcm.googleapis.com/fcm/send')
            ->setPayload(array_merge_recursive($payload, [
                'registration_ids' => [$token],
                'to'               => $topic,
            ]))
            ->setHeaders([
                'Content-Type'     => 'application/json',
                'Authorization'    => 'Key ' . config('chipolo-push.android.authorization-key'),
            ])->handle();

        if ($response->getStatusCode() != 200 && $this->repeated < 3) {
            $this->repeated++;
            $this->send($token, $payload, $topic);
        }

        $this->repeated = 0;
        return $response;
    }

    public function send1($token)
    {
        // $token = 'fPZOMpwBuSw:APA91bHn40aZNxKmeh45kNKi4aNmX4w_vSofPY3guibgo0xaJYLVLvPTuyJlg9iYkqFpzt9TWqfH8RW5hW7oF-TdJQSC_R5HZLHG7ri8f_Qfkdl_I2esbKRfsj4yUA2lGrwfKcYFT48_';
        $message   = 'Tralala';
        $serverKey = 'AAAAE4ujTck:APA91bHskVN-mmxRSgUETsj0Cf4JLBuK-yUGIUUU4Ca1PWuxMXHQrBw2OWx6P-wdlcS5tlSWpjCgbRdEOnJRgiWJ2sLx9fyPtbTUaMB1n_lBSZP0W5bGMVB5OjBDQVKero8LO7ZHTwexmbdF3C0eAXXJYlUw1vcs2g';

        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

        $fields = [
            'registration_ids' => [$token],
            'priority'         => 10,
            'notification'     => [
                'title' => 'Lala',
                'body'  => $message,
                'sound' => 'Default',
                'image' => 'Notification Image',
            ],
        ];
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type:application/json',
        ];

        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post
        $result = curl_exec($ch);
        // Close connection
        curl_close($ch);
        return $result;
    }
}
