<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class IncognitoModeException extends FingerprintException
{
    public static function incognitoModeDetected(): self
    {
        return new self('Incognito mode detected.');
    }
}
