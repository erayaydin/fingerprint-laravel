<?php

use ErayAydin\Fingerprint\Exceptions\VPNDetectedException;

it('throws an exception when VPN network is detected', function () {
    $exception = VPNDetectedException::vpnDetected();

    expect($exception)->toBeInstanceOf(VPNDetectedException::class)
        ->and($exception->getMessage())->toBe('VPN network detected.');
});
