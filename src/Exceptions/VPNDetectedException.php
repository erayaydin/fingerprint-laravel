<?php

namespace ErayAydin\Fingerprint\Exceptions;

final class VPNDetectedException extends FingerprintException
{
    /**
     * Throws an exception when the VPN network is detected.
     *
     * @return static The instance of the VPNDetectedException exception.
     */
    public static function vpnDetected(): self
    {
        return new self('VPN network detected.');
    }
}
