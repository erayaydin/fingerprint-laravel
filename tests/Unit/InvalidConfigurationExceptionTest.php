<?php

use ErayAydin\Fingerprint\Exceptions\InvalidConfiguration;

it('throws an exception when API key is not specified', function () {
    $exception = InvalidConfiguration::apiKeyNotSpecified();

    expect($exception)->toBeInstanceOf(InvalidConfiguration::class)
        ->and($exception->getMessage())->toBe('Fingerprint API key is not specified.');
});
