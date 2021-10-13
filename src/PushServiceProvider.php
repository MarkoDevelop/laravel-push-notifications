<?php

namespace Overthink\Push;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class PushServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/overthink-push.php' => config_path('overthink-push.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/overthink-push.php',
            'overthink-push'
        );
    }
}
