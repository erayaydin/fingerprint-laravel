<?php

use ErayAydin\Fingerprint\Exceptions\RegionNotSupportedException;

it('throws an exception when region is not supported', function () {
    $region = 'unknown';
    $exception = RegionNotSupportedException::regionNotSupported($region);

    expect($exception)->toBeInstanceOf(RegionNotSupportedException::class)
        ->and($exception->getMessage())->toBe("The region '$region' is not supported. Available regions are 'global', 'eu', 'ap'.");
});
