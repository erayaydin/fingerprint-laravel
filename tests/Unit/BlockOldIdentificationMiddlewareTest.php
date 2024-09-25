<?php

use Carbon\Carbon;
use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\OldIdentificationException;
use ErayAydin\Fingerprint\Http\Middleware\BlockOldIdentificationMiddleware;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->config = Mockery::mock(Repository::class);
    $this->request = Mockery::mock(Request::class);
    $this->next = fn ($request) => 'done';
});

it('allows request when identification is recent', function () {
    $event = Event::createFromEventResponse($this->getEventResponseMock(time: Carbon::now()->subMinutes(5)));
    $this->config->shouldReceive('get')->with('fingerprint.middleware.max_elapsed_time')->andReturn(new DateInterval('PT10M'));

    $response = (new BlockOldIdentificationMiddleware($event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when identification is old', function () {
    $event = Event::createFromEventResponse($this->getEventResponseMock(time: Carbon::now()->subMinutes(15)));
    $this->config->shouldReceive('get')->with('fingerprint.middleware.max_elapsed_time')->andReturn(new DateInterval('PT10M'));

    (new BlockOldIdentificationMiddleware($event, $this->config))($this->request, $this->next);
})->throws(OldIdentificationException::class);
