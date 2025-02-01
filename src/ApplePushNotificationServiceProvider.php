<?php

namespace RajChotaliya\ApplePushNotificationService;

use Illuminate\Support\ServiceProvider;

class ApplePushNotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config with application config
        $this->mergeConfigFrom(
            __DIR__ . '/config/apns.php', 'apns'
        );

        $this->app->singleton(ApplePushNotificationService::class, function ($app) {
            return new ApplePushNotificationService(
                null,
                'Hello from Raj!',
                'This is a test push notification.'
            );
        });
    }

    public function boot()
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/config/apns.php' => config_path('apns.php'),
        ], 'config');
    }
}
