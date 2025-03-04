<?php

namespace Alpha\Oauth\Facades;

use Illuminate\Support\Facades\Facade;

class SocialAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'social-auth';
    }
}