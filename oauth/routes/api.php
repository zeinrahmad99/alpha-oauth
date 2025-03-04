<?php

use Alpha\Oauth\Http\Controllers\SocialAuthController;

Route::post('/auth/{provider}', [SocialAuthController::class, 'handleAuth']);