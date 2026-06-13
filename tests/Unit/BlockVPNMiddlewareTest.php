<?php

use ErayAydin\Fingerprint\Exceptions\VPNDetectedException;
use ErayAydin\Fingerprint\Http\Middleware\BlockVPNMiddleware;
use Fingerprint\ServerSdk\Model\Event;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->event = Mockery::mock(Event::class);
    $this->config = Mockery::mock(Repository::class);
    $this->request = Mockery::mock(Request::class);
    $this->next = fn ($request) => 'done';
});

it('allows request when VPN is not detected', function () {
    $this->event->shouldReceive('getVpn')->andReturn(false);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.vpn_block')->andReturn(true);

    $response = (new BlockVPNMiddleware($this->event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when VPN is detected and blocking is enabled', function () {
    $this->event->shouldReceive('getVpn')->andReturn(true);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.vpn_block')->andReturn(true);

    (new BlockVPNMiddleware($this->event, $this->config))($this->request, $this->next);
})->throws(VPNDetectedException::class);

it('allows request when VPN is detected but blocking is disabled', function () {
    $this->event->shouldReceive('getVpn')->andReturn(true);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.vpn_block')->andReturn(false);

    $response = (new BlockVPNMiddleware($this->event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});
