<?php

namespace ErayAydin\Fingerprint\Enums;

enum BotBlockConfiguration
{
    /**
     * Blocks good and bad bots.
     */
    case BlockAll;
    /**
     * Blocks only bad bots.
     */
    case BlockBad;
    /**
     * Allow all bots to access.
     */
    case Allow;
}
