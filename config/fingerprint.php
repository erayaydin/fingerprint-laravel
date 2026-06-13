<?php

use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use ErayAydin\Fingerprint\Http\Middleware\BlockBotsMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockIncognitoMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockOldIdentificationMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockTorMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockVPNMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\MinConfidenceScoreMiddleware;

return [
    /**
     * The secret API key for the Fingerprint Pro service.
     */
    'api_secret' => env('FINGERPRINT_PRO_SECRET_API_KEY'),

    /**
     * The query parameter name used to read the event ID from the incoming request.
     *
     * When null, the package looks for `event_id` first, then falls back to `requestId`
     * for backward compatibility.
     *
     * Default: null
     */
    'event_id_param' => env('FINGERPRINT_EVENT_ID_PARAM'),

    /**
     * The region of the Fingerprint Pro service.
     *
     * Default: global
     *
     * Available options: `global`/`us`, `eu`/`europe`, `ap`/`asia`
     */
    'region' => env('FINGERPRINT_REGION', 'global'),

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
         * @see BlockBotsMiddleware
         */
        'bot_block' => BotBlockConfiguration::BlockBad,

        /**
         * Blocks request if user is using a VPN.
         *
         * Default: true
         *
         * @see BlockVPNMiddleware::Class
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
         * @see BlockTorMiddleware::class
         */
        'tor_block' => TorBlockConfiguration::BlockIfSignaled,

        /**
         * Minimum required confidence score. It should be in range of 0.0 to 1.0. If it's null,
         * it will not check the confidence score.
         *
         * Default: 0.8
         *
         * @see MinConfidenceScoreMiddleware::class
         */
        'min_confidence' => 0.8,

        /**
         * Blocks users who are using incognito mode.
         *
         * Default: true
         *
         * @see BlockIncognitoMiddleware::class
         */
        'incognito_block' => true,

        /**
         * Maximum elapsed time between the request and the event identification.
         *
         * Default: 10 seconds
         *
         * @see BlockOldIdentificationMiddleware::class
         * @see DateInterval
         * @link https://php.net/manual/en/dateinterval.construct.php
         */
        'max_elapsed_time' => new DateInterval('PT10S'),

    ],
];
