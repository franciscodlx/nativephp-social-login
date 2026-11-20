<?php

/*
| Client IDs per provider. Leave one empty to try that provider against the
| local mocks (tools/mock-idp + tools/<provider>-backend); fill it in, and its
| secret in the backend's .env, to use the real service. Only public values:
| client secrets live in the backends.
*/

return [
    'client_ids' => [
        'linkedin' => env('DEMO_LINKEDIN_CLIENT_ID', ''),
        // X is a public client: no backend, the app exchanges the code (PKCE).
        'x' => env('DEMO_X_CLIENT_ID', ''),
        'mercadolibre' => env('DEMO_MERCADOLIBRE_CLIENT_ID', ''),
        'twitch' => env('DEMO_TWITCH_CLIENT_ID', ''),
        'github' => env('DEMO_GITHUB_CLIENT_ID', ''),
        'discord' => env('DEMO_DISCORD_CLIENT_ID', ''),
        'slack' => env('DEMO_SLACK_CLIENT_ID', ''),
        'strava' => env('DEMO_STRAVA_CLIENT_ID', ''),
        // Bot ID from @BotFather.
        'telegram' => env('DEMO_TELEGRAM_CLIENT_ID', ''),
        // "Client key" in the TikTok developer portal.
        'tiktok' => env('DEMO_TIKTOK_CLIENT_KEY', ''),
    ],

    // Public https URL (tunnel) of each backend that has real credentials.
    'backend_public_urls' => [
        'linkedin' => env('DEMO_LINKEDIN_BACKEND_URL', ''),
        'mercadolibre' => env('DEMO_MERCADOLIBRE_BACKEND_URL', ''),
        'twitch' => env('DEMO_TWITCH_BACKEND_URL', ''),
        'presets' => env('DEMO_PRESETS_BACKEND_URL', ''),
        'telegram' => env('DEMO_TELEGRAM_BACKEND_URL', ''),
        'tiktok' => env('DEMO_TIKTOK_BACKEND_URL', ''),
    ],

    'local_backend_ports' => [
        'linkedin' => 4100,
        'mercadolibre' => 4200,
        'twitch' => 4300,
        'presets' => 4400,
        'telegram' => 4500,
        'tiktok' => 4600,
    ],

    // Same schemes as the React Native and Flutter examples (see
    // SOCIAL_LOGIN_REDIRECT_SCHEMES in .env).
    'app_callbacks' => [
        'linkedin' => 'linkedinexample://callback',
        'x' => 'xloginexample://callback',
        'mercadolibre' => 'meliexample://callback',
        'twitch' => 'twitchexample://callback',
        'presets' => 'presetsexample://callback',
        'telegram' => 'telegramexample://callback',
        'tiktok' => 'tiktokexample://callback',
    ],
];
