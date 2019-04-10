<?php

namespace Chipolo\Push;

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
            __DIR__ . '/../config/chiopolo-push.php' => config_path('chiopolo-push.php'),
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
            __DIR__ . '/../config/chipolo-push.php',
            'chipolo-push'
        );
    }
}
