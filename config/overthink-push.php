<?php

return [
    'general' => [
        'keep_alive' => true,
    ],
    'ios'     => [
        'certificate-path' => env('PUSH_IOS_AUTH_KEY_PATH'),
        'secret'           => env('PUSH_IOS_SECRET'),
        'team-id'          => env('PUSH_IOS_TEAM_ID'),
    ],
    'android' => [
        'authorization-key'              => env('PUSH_ANDROID_AUTH_KEY'),
        'google_application_credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
        'project_id'                     => env('PUSH_ANDROID_PROJECT_ID'),
    ],
];
