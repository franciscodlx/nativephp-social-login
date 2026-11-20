<?php

namespace App\Native;

use Franciscodlxdev\SocialLogin\Facades\SocialLogin;
use Franciscodlxdev\SocialLogin\ProviderConfig;
use Franciscodlxdev\SocialLogin\Providers\X;
use Franciscodlxdev\SocialLogin\SignInResult;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Facades\System;
use RuntimeException;

/**
 * Every provider of the demo: which backend exchanges its code, and whether
 * it talks to the real service (a client ID is configured) or to the mocks.
 */
final class DemoProviders
{
    /** id => [label, backend (null: exchanged on the device)] */
    public const ALL = [
        'linkedin' => ['LinkedIn', 'linkedin'],
        'x' => ['X', null],
        'mercadolibre' => ['Mercado Libre', 'mercadolibre'],
        'twitch' => ['Twitch', 'twitch'],
        'github' => ['GitHub', 'presets'],
        'discord' => ['Discord', 'presets'],
        'slack' => ['Slack', 'presets'],
        'strava' => ['Strava', 'presets'],
        'telegram' => ['Telegram', 'telegram'],
        'tiktok' => ['TikTok', 'tiktok'],
    ];

    public static function isReal(string $id): bool
    {
        return config("social-demo.client_ids.{$id}", '') !== '';
    }

    /** The Android emulator reaches the host machine through 10.0.2.2. */
    public static function host(): string
    {
        return System::isAndroid() ? '10.0.2.2' : 'localhost';
    }

    public static function backendUrl(string $backend, bool $real): string
    {
        return $real
            ? rtrim((string) config("social-demo.backend_public_urls.{$backend}"), '/')
            : 'http://'.self::host().':'.config("social-demo.local_backend_ports.{$backend}");
    }

    /** Opens the browser; the result arrives as AuthSessionCompleted. */
    public static function signIn(string $id, bool $ephemeral = false): void
    {
        [, $backend] = self::ALL[$id];
        $real = self::isReal($id);
        $mockIdp = 'http://'.self::host().':4000';
        $clientId = $real ? config("social-demo.client_ids.{$id}") : ($backend === null ? 'example-app' : 'confidential-app');

        if ($backend === null) {
            // X: public client, the app exchanges the code itself.
            $provider = SocialLogin::x($real ? [] : [
                'authorizationEndpoint' => "{$mockIdp}/authorize",
                'tokenEndpoint' => "{$mockIdp}/token",
                'userInfoEndpoint' => "{$mockIdp}/2/users/me",
                'revocationEndpoint' => "{$mockIdp}/revoke",
            ]);
            SocialLogin::signIn($provider, [
                'clientId' => $clientId,
                'redirectUri' => config('social-demo.app_callbacks.x'),
                'scopes' => ['users.read', 'tweet.read', X::EMAIL_SCOPE, 'offline.access'],
                'exchangeCode' => true,
                'prefersEphemeralSession' => $ephemeral,
            ]);

            return;
        }

        $base = self::backendUrl($backend, $real);
        $mock = $real ? [] : ['authorizationEndpoint' => "{$mockIdp}/authorize"];
        SocialLogin::signIn(self::provider($id, $mock), [
            'clientId' => $clientId,
            'redirectUri' => "{$base}/auth/{$id}/callback",
            'callbackUri' => config("social-demo.app_callbacks.{$backend}"),
            'scopes' => $id === 'telegram' ? ['openid', 'profile', 'phone'] : null,
            'prefersEphemeralSession' => $ephemeral,
        ]);
    }

    /**
     * Turns the completed sign-in into what the screen shows: the profile
     * from the device exchange (X) or from the backend.
     *
     * @return array{provider: string, id: string, name: ?string, username: ?string, email: ?string}
     */
    public static function finish(SignInResult $result): array
    {
        [$label, $backend] = self::ALL[$result->provider];
        if ($backend === null) {
            $profile = $result->profile ?? throw new RuntimeException('X returned no profile');
        } else {
            // The code is useless to the app: the backend swaps it for the profile.
            $base = self::backendUrl($backend, self::isReal($result->provider));
            $response = Http::acceptJson()->timeout(30)
                ->post("{$base}/auth/{$result->provider}/exchange", $result->forBackend());
            if (! $response->successful()) {
                throw new RuntimeException('BACKEND ERROR: '.($response->json('error_description') ?? $response->json('error') ?? 'Exchange failed'));
            }
            $profile = $response->json('profile') ?? [];
        }

        $text = static fn ($value) => is_string($value) && $value !== '' ? $value : null;

        return [
            'provider' => $label,
            'id' => (string) ($profile['id'] ?? $profile['sub'] ?? ''),
            'name' => $text($profile['name'] ?? null),
            'username' => $text($profile['username'] ?? $profile['nickname'] ?? null),
            'email' => $text($profile['email'] ?? null),
        ];
    }

    private static function provider(string $id, array $overrides): ProviderConfig
    {
        return match ($id) {
            'linkedin' => SocialLogin::linkedin($overrides),
            'mercadolibre' => SocialLogin::mercadolibre('MX', $overrides),
            'twitch' => SocialLogin::twitch($overrides),
            'telegram' => SocialLogin::telegram($overrides),
            'tiktok' => SocialLogin::tiktok($overrides),
            default => SocialLogin::preset($id, $overrides),
        };
    }
}
