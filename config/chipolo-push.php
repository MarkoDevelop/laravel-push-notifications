<?php

return [
    'general' => [
        'keep-alive' => true,
    ],
    'ios'     => [
        'certificate-path' => base_path('key_chipolo.p8'),
        'secret'           => env('PUSH-IOS-SECRET'),
        'team-id'          => env('PUSH-IOS-TEAM-ID'),
    ],
    'android' => [
        'authorization-key' => env('PUSH-ANDROID-AUTH-KEY'),
    ],
];
