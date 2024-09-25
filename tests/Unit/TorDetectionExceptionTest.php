<?php

use ErayAydin\Fingerprint\Exceptions\TorDetectionException;

it('throws an exception when Tor network is detected', function () {
    $exception = TorDetectionException::torDetected();

    expect($exception)->toBeInstanceOf(TorDetectionException::class)
        ->and($exception->getMessage())->toBe('Tor network detected.');
});

it('throws an exception when Tor detection is required', function () {
    $exception = TorDetectionException::torDetectionRequired();

    expect($exception)->toBeInstanceOf(TorDetectionException::class)
        ->and($exception->getMessage())->toBe('Tor detection required.');
});
