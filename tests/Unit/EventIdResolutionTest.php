<?php

use ErayAydin\Fingerprint\Fingerprint;
use Fingerprint\ServerSdk\Model\Event as SdkEvent;
use Illuminate\Http\Request;

beforeEach(function () {
    config()->set('fingerprint.api_secret', 'secret');
    config()->set('fingerprint.region', 'global');

    $mockFingerprint = Mockery::mock(Fingerprint::class);
    $mockFingerprint->shouldReceive('getEvent')->andReturnUsing(
        fn (string $id) => $this->getSdkEventMock(eventId: $id)
    );
    $this->app->instance(Fingerprint::class, $mockFingerprint);
});

it('resolves event_id from request when no param is configured', function () {
    config()->set('fingerprint.event_id_param');
    $this->app->instance('request', Request::create('/', 'GET', ['event_id' => 'abc123']));

    $event = $this->app->make(SdkEvent::class);

    expect($event->getEventId())->toBe('abc123');
});

it('falls back to requestId when event_id is absent', function () {
    config()->set('fingerprint.event_id_param');
    $this->app->instance('request', Request::create('/', 'GET', ['requestId' => 'legacy123']));

    $event = $this->app->make(SdkEvent::class);

    expect($event->getEventId())->toBe('legacy123');
});

it('prefers event_id over requestId when both are present', function () {
    config()->set('fingerprint.event_id_param');
    $this->app->instance('request', Request::create('/', 'GET', ['event_id' => 'new123', 'requestId' => 'old123']));

    $event = $this->app->make(SdkEvent::class);

    expect($event->getEventId())->toBe('new123');
});

it('uses the configured param name', function () {
    config()->set('fingerprint.event_id_param', 'my_event');
    $this->app->instance('request', Request::create('/', 'GET', ['my_event' => 'custom123', 'event_id' => 'ignored']));

    $event = $this->app->make(SdkEvent::class);

    expect($event->getEventId())->toBe('custom123');
});

it('returns null when configured param is absent from request', function () {
    config()->set('fingerprint.event_id_param', 'my_event');
    $this->app->instance('request', Request::create('/', 'GET', ['event_id' => 'ignored']));

    expect($this->app->make(SdkEvent::class))->toBeNull();
});

it('returns null when no event id is present', function () {
    config()->set('fingerprint.event_id_param');
    $this->app->instance('request', Request::create('/'));

    expect($this->app->make(SdkEvent::class))->toBeNull();
});
