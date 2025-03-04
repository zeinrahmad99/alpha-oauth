<?php

namespace Alpha\Oauth\Contracts;

interface SocialAuthInterface
{
    public function handleSocialAuth(string $provider, string $token): array;
}