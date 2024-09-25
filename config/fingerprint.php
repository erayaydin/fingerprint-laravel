<?php

use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;

return [
    /**
     * The secret API key for the Fingerprint Pro service.
     */
    'api_secret' => env('FINGERPRINT_PRO_SECRET_API_KEY'),

    /**
     * The region of the Fingerprint Pro service.
     *
     * Default: eu
     *
     * Available options: `eu`/`europe`, `ap`/`asia`, `global`
     */
    'region' => env('FINGERPRINT_REGION', 'eu'),

    /**
     * Fingerprint middleware configuration
     */
    'middleware' => [

        /**
         * Blocks good and/or bad bots.
         *
         * BotBlockConfiguration::BlockAll => Blocks good and bad bots.
         * BotBlockConfiguration::BlockBad => Blocks only bad bots.
         * BotBlockConfiguration::Allow    => Disable middleware.
         *
         * Default: BotBlockConfiguration::BlockBad
         *
         * @see \ErayAydin\Fingerprint\Http\Middleware\BlockBotsMiddleware
         */
        'bot_block' => BotBlockConfiguration::BlockBad,

        /**
         * Blocks request if user is using a VPN.
         *
         * Default: true
         *
         * @see \ErayAydin\Fingerprint\Http\Middleware\BlockVPNMiddleware::Class
         */
        'vpn_block' => true,

        /**
         * Blocks tor network users.
         *
         * TorBlockConfiguration::BlockAll => Blocks request even event doesn't have a tor signal.
         * TorBlockConfiguration::BlockIfSignaled => Blocks request if event has a true tor signal.
         * TorBlockConfiguration::Allow => Disable middleware.
         *
         * Default: TorBlockConfiguration::BlockIfSignaled
         *
         * @see \ErayAydin\Fingerprint\Http\Middleware\BlockTorMiddleware::class
         */
        'tor_block' => TorBlockConfiguration::BlockIfSignaled,

        /**
         * Minimum required confidence score. It should be in range of 0.0 to 1.0. If it's null,
         * it will not check the confidence score.
         *
         * Default: 0.8
         *
         * @see \ErayAydin\Fingerprint\Http\Middleware\MinConfidenceScoreMiddleware::class
         */
        'min_confidence' => 0.8,

        /**
         * Blocks users who are using incognito mode.
         *
         * Default: true
         *
         * @see \ErayAydin\Fingerprint\Http\Middleware\BlockIncognitoMiddleware::class
         */
        'incognito_block' => true,

        /**
         * Maximum elapsed time between the request and the event identification.
         *
         * Default: 10 seconds
         *
         * @see \ErayAydin\Fingerprint\Http\Middleware\BlockOldIdentificationMiddleware::class
         * @see DateInterval
         * @link https://php.net/manual/en/dateinterval.construct.php
         */
        'max_elapsed_time' => new DateInterval('PT10S'),

    ],
];
