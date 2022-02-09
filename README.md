# Send pushes with Laravel

This is a wrapper around 

## Installation

You can install the package via composer:

```bash
composer require overthink/laravel-push-notifications
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-push-notifications"
```

This is the contents of the published config file:

```php
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
```
### iOS
You have to setup certificate from Apple and link its path in `PUSH_IOS_AUTH_KEY_PATH`. You can check the instructions [here](https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/establishing_a_certificate-based_connection_to_apns).

For the `PUSH_IOS_SECRET` you can follow this [stack overflow answer](https://stackoverflow.com/a/40301501), you are looking for `kid` value.

`PUSH_IOS_TEAM_ID` can be found in Apple Developer account as a part of mobile developers data.

### Android
`PUSH_ANDROID_AUTH_KEY` is no longer used in this project, and will be removed in the next version.

`GOOGLE_APPLICATION_CREDENTIALS` can be created if you follow instructions [here](https://developers.google.com/workspace/guides/create-credentials).

`PUSH_ANDROID_PROJECT_ID` is a unique identifier for your Firebase project, used in requests to the FCM v1 HTTP endpoint. This value is available in the [Firebase console Settings](https://console.firebase.google.com/project/_/settings/general) pane.

## Usage
We need a device object from where we get all the data that we use for sending a push. In most cases this can be Laravel model.
```php
use Overthink\Push\Contracts\DeviceContract;

class Device implements DeviceContract
{    
    public function getOperatingSystem(): string
    {
        return 'android'; // or 'ios'
    }

    public function getPushToken(): string
    {
        // Push token should be provided by mobile application
        return '8516cdd38e63170237df6e7eb6f22d875a5e07c10082...';
    }

    public function isDevelopment(): bool
    {
        // With this value you can set development or production api when sending iOS push
        return false;
    }

    public function getTopic(): string
    {
        // Mobile applications unique identifier used when sending iOS push 
        return 'com.example.test'
    }

}

```
Here we combine all the parts to send push to either Android or iOS device.

```php
$androidExamplePush = [
    'message' => [
        'android' => [
            'priority' => 'normal',
            'data'     => [
                'title'      => 'Example Android title',
                'body'       => 'Example Android body',
                'extra_data' => 'Some extra data that you want to pass to mobile app'
            ],
        ],
    ],
];

$iosExamplePush = [
  'aps' => [
      'sound' => 'short_sound.caf', // sound file needs to present in the app
      'alert' => [
        'title' => 'Example iOS title',
        'body'  => 'Example iOS body'
      ],
  ],
  'extra_data' => 'Some extra data that you want to pass to mobile app'
];

$pushPayload = (new PushPayload())
           ->setAndroidPayload($androidExamplePush)
           ->setIosPayload($iosExamplePush);

// We need collection of objects that implement Overthink\Push\Contracts\DeviceContract
$devices = collect([new Device()]);

$push = (new Overthink\Push\Push())
      ->setPushPayload($pushPayload)
      ->setDevices($devices)
      ->send();

```

## Credits

- [Marko Zagar](https://github.com/MarkoDevelop)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
