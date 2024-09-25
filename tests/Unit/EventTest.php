<?php

use ErayAydin\Fingerprint\Enums\BotDResult;
use ErayAydin\Fingerprint\Event;

it('creates an Event instance from an EventResponse model', function () {
    $event = Event::createFromEventResponse(
        $this->getEventResponseMock(incognito: true, botDResult: 'bad', isTor: true)
    );

    expect($event->identification->requestId)->toBe('request-id')
        ->and($event->identification->visitorId)->toBe('visitor-id')
        ->and($event->identification->incognito)->toBeTrue()
        ->and($event->identification->url)->toBe('https://example.com')
        ->and($event->identification->ip)->toBe('127.0.0.1')
        ->and($event->identification->confidence)->toBe(0.9)
        ->and($event->botD)->toBe(BotDResult::Bad)
        ->and($event->isTor)->toBeTrue()
        ->and($event->isVPN)->toBeFalse();
});

it('creates an Event instance with good bot', function () {
    $event = Event::createFromEventResponse(
        $this->getEventResponseMock(incognito: true, botDResult: 'good', isTor: true)
    );

    expect($event->identification->requestId)->toBe('request-id')
        ->and($event->identification->visitorId)->toBe('visitor-id')
        ->and($event->identification->incognito)->toBeTrue()
        ->and($event->identification->url)->toBe('https://example.com')
        ->and($event->identification->ip)->toBe('127.0.0.1')
        ->and($event->identification->confidence)->toBe(0.9)
        ->and($event->botD)->toBe(BotDResult::Good)
        ->and($event->isTor)->toBeTrue()
        ->and($event->isVPN)->toBeFalse();
});

it('creates an Event instance with not detected bot', function () {
    $event = Event::createFromEventResponse(
        $this->getEventResponseMock(incognito: true, isTor: true)
    );

    expect($event->identification->requestId)->toBe('request-id')
        ->and($event->identification->visitorId)->toBe('visitor-id')
        ->and($event->identification->incognito)->toBeTrue()
        ->and($event->identification->url)->toBe('https://example.com')
        ->and($event->identification->ip)->toBe('127.0.0.1')
        ->and($event->identification->confidence)->toBe(0.9)
        ->and($event->botD)->toBe(BotDResult::NotDetected)
        ->and($event->isTor)->toBeTrue()
        ->and($event->isVPN)->toBeFalse();
});
