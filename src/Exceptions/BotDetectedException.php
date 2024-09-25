<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class BotDetectedException extends FingerprintException
{
    /**
     * Throws an exception when bot detected.
     *
     * @return static The instance of the TorDetectionException exception.
     */
    public static function botDetected(): self
    {
        return new self('Bot detected.');
    }

    /**
     * Throws an exception when bad bot detected.
     *
     * @return static The instance of the TorDetectionException exception.
     */
    public static function badBotDetected(): self
    {
        return new self('Bad bot detected.');
    }
}
