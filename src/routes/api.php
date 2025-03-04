<?php

use Alpha\Oauth\Http\Controllers\SocialAuthController;
use Illuminate\Routing\Route;


Route::post('/auth/{provider}', [SocialAuthController::class, 'handleAuth']);