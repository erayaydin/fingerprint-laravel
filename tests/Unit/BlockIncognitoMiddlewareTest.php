<?php

use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\IncognitoModeException;
use ErayAydin\Fingerprint\Http\Middleware\BlockIncognitoMiddleware;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->config = Mockery::mock(Repository::class);
    $this->request = Mockery::mock(Request::class);
    $this->next = fn ($request) => 'done';
});

it('allows request when not in incognito mode', function () {
    $event = Event::createFromEventResponse($this->getEventResponseMock());
    $this->config->shouldReceive('get')->with('fingerprint.middleware.incognito_block')->andReturn(true);

    $response = (new BlockIncognitoMiddleware($event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when in incognito mode', function () {
    $event = Event::createFromEventResponse($this->getEventResponseMock(incognito: true));
    $this->config->shouldReceive('get')->with('fingerprint.middleware.incognito_block')->andReturn(true);

    (new BlockIncognitoMiddleware($event, $this->config))($this->request, $this->next);
})->throws(IncognitoModeException::class);

it('allows request when incognito blocking is disabled', function () {
    $event = Event::createFromEventResponse($this->getEventResponseMock(incognito: true));
    $this->config->shouldReceive('get')->with('fingerprint.middleware.incognito_block')->andReturn(false);

    $response = (new BlockIncognitoMiddleware($event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});
