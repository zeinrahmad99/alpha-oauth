<?php

namespace Alpha\Oauth\Providers;

use Illuminate\Support\ServiceProvider;
use Alpha\Oauth\Services\SocialAuthService;
use Alpha\Oauth\Contracts\SocialAuthInterface;

class OauthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SocialAuthInterface::class, SocialAuthService::class);
        $this->app->singleton('social-auth', function () {
            return new SocialAuthService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/oauth.php' => config_path('oauth.php'),
        ], 'oauth-config');
    }
}