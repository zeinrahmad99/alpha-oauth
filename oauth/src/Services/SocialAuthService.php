<?php

namespace Alpha\Oauth\Services;

use Alpha\Oauth\Contracts\SocialAuthInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;

class SocialAuthService implements SocialAuthInterface
{
    public function handleSocialAuth(string $provider, string $token): array
    {
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            throw new \InvalidArgumentException('Invalid provider');
        }

        $userInfo = $this->getUserInfoFromToken($provider, $token);

        return [
            'name' => $userInfo['name'] ?? 'Unknown',
            'email' => $userInfo['email'],
            'provider_id' => $userInfo['provider_id'],
            'avatar' => $userInfo['avatar'] ?? null,
        ];
    }

    protected function getUserInfoFromToken(string $provider, string $token): array
    {
        $cacheKey = "social_auth_{$provider}_" . md5($token);
        return Cache::remember($cacheKey, 3600, function () use ($provider, $token) {
            switch ($provider) {
                case 'google':
                    return $this->getGoogleUserInfo($token);
                case 'facebook':
                    return $this->getFacebookUserInfo($token);
                case 'apple':
                    return $this->getAppleUserInfo($token);
                default:
                    throw new \InvalidArgumentException('Unsupported provider');
            }
        });
    }

    protected function getGoogleUserInfo(string $token): array
    {
        $response = Http::get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
            'id_token' => $token,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Failed to fetch user info from Google');
        }

        $data = $response->json();

        return [
            'name' => $data['name'] ?? 'Unknown',
            'email' => $data['email'],
            'provider_id' => $data['sub'],
            'avatar' => $data['picture'] ?? null,
        ];
    }

    protected function getFacebookUserInfo(string $token): array
    {
        $response = Http::get('https://graph.facebook.com/v12.0/me', [
            'fields' => 'id,name,email,picture',
            'access_token' => $token,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Failed to fetch user info from Facebook');
        }

        $data = $response->json();

        return [
            'name' => $data['name'] ?? 'Unknown',
            'email' => $data['email'],
            'provider_id' => $data['id'],
            'avatar' => $data['picture']['data']['url'] ?? null,
        ];
    }

    protected function getAppleUserInfo(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \RuntimeException('Invalid Apple token');
        }

        $payload = base64_decode($parts[1]);
        $data = json_decode($payload, true);

        return [
            'name' => 'Unknown',
            'email' => $data['email'],
            'provider_id' => $data['sub'],
            'avatar' => null,
        ];
    }
}