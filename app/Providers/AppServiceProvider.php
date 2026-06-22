<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            [\SocialiteProviders\Discord\DiscordExtendSocialite::class, 'handle']
        );

        // Suppress Stripe outdated API version notices from triggering exceptions in Laravel
        set_error_handler(function ($severity, $message, $file, $line) {
            if ($severity === E_USER_NOTICE && str_contains($file, 'stripe-php')) {
                return true; // Silence notice
            }
            return false; // Fall back to default error handler
        }, E_USER_NOTICE);
    }
}
