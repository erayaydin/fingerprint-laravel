<?php

use ErayAydin\Fingerprint\Console\AboutCommandRegistrar;

it('registers the About command', function () {
    $configRepository = $this->getConfigRepository();

    AboutCommandRegistrar::register($configRepository);

    $this->artisan('about')
        ->expectsOutputToContain('Fingerprint Laravel')
        ->expectsOutputToContain('API Key')
        ->expectsOutputToContain('Region')
        ->expectsOutputToContain('Bot Block')
        ->expectsOutputToContain('VPN Block')
        ->expectsOutputToContain('TOR Block')
        ->expectsOutputToContain('Min. Confidence')
        ->expectsOutputToContain('Incognito Block')
        ->expectsOutputToContain('Old Identification')
        ->expectsOutputToContain('Version')
        ->assertExitCode(0);
});
