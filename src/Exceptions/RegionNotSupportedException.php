<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class RegionNotSupportedException extends FingerprintException
{
    public static function regionNotSupported(string $region): self
    {
        return new self("The region '$region' is not supported. Available regions are 'global', 'eu', 'ap'.");
    }
}
