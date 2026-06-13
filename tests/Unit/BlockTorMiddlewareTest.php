<?php

use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use ErayAydin\Fingerprint\Exceptions\TorDetectionException;
use ErayAydin\Fingerprint\Http\Middleware\BlockTorMiddleware;
use Fingerprint\ServerSdk\Model\Event;
use Fingerprint\ServerSdk\Model\IPBlockList;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->config = Mockery::mock(Repository::class);
    $this->request = Mockery::mock(Request::class);
    $this->next = fn ($request) => 'done';
});

function makeEventWithTorNode(?bool $torNode): Event
{
    $ipBlocklist = Mockery::mock(IPBlockList::class);
    $ipBlocklist->shouldReceive('getTorNode')->andReturn($torNode);

    $event = Mockery::mock(Event::class);
    $event->shouldReceive('getIpBlocklist')->andReturn($ipBlocklist);

    return $event;
}

it('allows request when tor is not detected', function () {
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockAll);

    $response = (new BlockTorMiddleware(makeEventWithTorNode(false), $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when Tor is detected and configuration is BlockAll', function () {
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockAll);

    (new BlockTorMiddleware(makeEventWithTorNode(true), $this->config))($this->request, $this->next);
})->throws(TorDetectionException::class);

it('blocks request when Tor signal is absent and configuration is BlockAll', function () {
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockAll);

    (new BlockTorMiddleware(makeEventWithTorNode(null), $this->config))($this->request, $this->next);
})->throws(TorDetectionException::class);

it('allows request when Tor is not detected and configuration is BlockIfSignaled', function () {
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockIfSignaled);

    $response = (new BlockTorMiddleware(makeEventWithTorNode(null), $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when Tor is detected and configuration is BlockIfSignaled', function () {
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::BlockIfSignaled);

    (new BlockTorMiddleware(makeEventWithTorNode(true), $this->config))($this->request, $this->next);
})->throws(TorDetectionException::class);

it('allows request when configuration is Allow', function () {
    $this->config->shouldReceive('get')->with('fingerprint.middleware.tor_block')->andReturn(TorBlockConfiguration::Allow);

    $response = (new BlockTorMiddleware(makeEventWithTorNode(true), $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});
