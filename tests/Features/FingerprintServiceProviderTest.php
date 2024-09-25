<?php

use ErayAydin\Fingerprint\Exceptions\InvalidConfiguration;
use ErayAydin\Fingerprint\Fingerprint;
use Illuminate\Contracts\Config\Repository;

beforeEach(function () {
    $this->config = $this->app->make(Repository::class);
});

it('registers the Fingerprint service', function () {
    $this->config->set('fingerprint.api_secret', 'test_api_key');
    $this->config->set('fingerprint.region', 'global');

    $fingerprint = $this->app->make(Fingerprint::class);

    expect($fingerprint)->toBeInstanceOf(Fingerprint::class);
});

it('throws InvalidConfiguration if API key is not specified', function () {
    $this->config->set('fingerprint.api_secret', null);

    $this->expectException(InvalidConfiguration::class);

    $this->app->make(Fingerprint::class);
});
