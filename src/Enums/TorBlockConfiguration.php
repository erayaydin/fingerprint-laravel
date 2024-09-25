<?php

namespace ErayAydin\Fingerprint\Enums;

enum TorBlockConfiguration
{
    /**
     * Blocks request even event doesn't have a tor signal
     */
    case BlockAll;
    /**
     * Blocks request if event has a true tor signal
     */
    case BlockIfSignaled;
    /**
     * Allows users to use tor network and browser
     */
    case Allow;
}
