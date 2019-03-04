<?php

return [
    'general' => [
        'keep-alive' => true,
    ],
    'ios'     => [
        'certificate-path' => base_path('key_chipolo.p8'),
        'secret'           => env('PUSH_IOS_SECRET'),
        'team-id'          => env('PUSH_IOS_TEAM_ID'),
    ],
    'android' => [
        'authorization-key'              => env('PUSH-ANDROID-AUTH-KEY'),
        'google_application_credentials' => base_path('firebase_service_account_chipolo.json'),
    ],
];
