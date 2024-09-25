<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class OldIdentificationException extends FingerprintException
{
    public static function oldIdentification(): self
    {
        return new self('The identification is too old.');
    }
}
