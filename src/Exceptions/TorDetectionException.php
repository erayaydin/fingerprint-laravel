<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class TorDetectionException extends FingerprintException
{
    /**
     * Throws an exception when the Tor network is detected.
     *
     * @return static The instance of the TorDetectionException exception.
     */
    public static function torDetected(): self
    {
        return new self('Tor network detected.');
    }

    /**
     * Throws an exception when the Tor network detection is required.
     *
     * @return static The instance of the TorDetectionException exception.
     */
    public static function torDetectionRequired(): self
    {
        return new self('Tor detection required.');
    }
}
