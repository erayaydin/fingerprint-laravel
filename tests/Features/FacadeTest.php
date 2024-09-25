<?php

use ErayAydin\Fingerprint\Facades\Fingerprint;
use ErayAydin\Fingerprint\Fingerprint as FingerprintService;
use Illuminate\Support\Facades\Facade;

beforeEach(function () {
    Facade::clearResolvedInstances();
});

it('resolves the Fingerprint service from the container', function () {
    $fingerprintService = Mockery::mock(FingerprintService::class);
    $this->app->instance('fingerprint', $fingerprintService);

    expect(Fingerprint::getFacadeRoot())->toBe($fingerprintService);
});
