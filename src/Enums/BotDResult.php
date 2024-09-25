<?php

namespace ErayAydin\Fingerprint\Enums;

enum BotDResult: string
{
    /**
     * Request is not sent by any good or bad bot.
     */
    case NotDetected = 'notDetected';
    /**
     * Request is sent by a good bot.
     */
    case Good = 'good';
    /**
     * Request is sent by a bad bot.
     */
    case Bad = 'bad';
}
