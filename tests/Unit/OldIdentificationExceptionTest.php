<?php

use ErayAydin\Fingerprint\Exceptions\OldIdentificationException;

it('throws an exception when identification too old', function () {
    $exception = OldIdentificationException::oldIdentification();

    expect($exception)->toBeInstanceOf(OldIdentificationException::class)
        ->and($exception->getMessage())->toBe('The identification is too old.');
});
