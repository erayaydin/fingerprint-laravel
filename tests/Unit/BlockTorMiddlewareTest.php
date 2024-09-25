<?php

use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\TorDetectionException;
use ErayAydin\Fingerprint\Http\Middleware\BlockTorMiddleware;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->event = Mockery::mock(Event::class);
    $this->config = Mockery::mock(Repository::class);
    $this->request = Mockery::mock(Request::class);
    $this->next = fn ($request) => 'done';
});

it('allows request when tor is not detected', function () {
    $this->event->isTor = false;
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockAll);

    $response = (new BlockTorMiddleware($this->event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when Tor is detected and configuration is BlockAll', function () {
    $this->event->isTor = true;
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockAll);

    (new BlockTorMiddleware($this->event, $this->config))($this->request, $this->next);
})->throws(TorDetectionException::class);

it('allows request when Tor is not detected and configuration is BlockIfSignaled', function () {
    $this->event->isTor = null;
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockIfSignaled);

    $response = (new BlockTorMiddleware($this->event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when Tor is detected and configuration is BlockIfSignaled', function () {
    $this->event->isTor = true;
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockIfSignaled);

    (new BlockTorMiddleware($this->event, $this->config))($this->request, $this->next);
})->throws(TorDetectionException::class);
