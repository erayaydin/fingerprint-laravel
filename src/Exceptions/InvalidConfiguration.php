<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class InvalidConfiguration extends FingerprintException
{
    /**
     * Throws an exception when the Fingerprint API key is not specified.
     *
     * @return static The instance of the InvalidConfiguration exception.
     */
    public static function apiKeyNotSpecified(): self
    {
        return new self('Fingerprint API key is not specified.');
    }
}
