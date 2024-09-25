<?php

use ErayAydin\Fingerprint\Exceptions\IncognitoModeException;

it('throws an exception when incognito mode detected', function () {
    $exception = IncognitoModeException::incognitoModeDetected();

    expect($exception)->toBeInstanceOf(IncognitoModeException::class)
        ->and($exception->getMessage())->toBe('Incognito mode detected.');
});
