<?php

use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Exceptions\BotDetectedException;
use ErayAydin\Fingerprint\Http\Middleware\BlockBotsMiddleware;
use Fingerprint\ServerSdk\Model\BotResult;
use Fingerprint\ServerSdk\Model\Event;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->event = Mockery::mock(Event::class);
    $this->config = Mockery::mock(Repository::class);
    $this->request = Mockery::mock(Request::class);
    $this->next = fn ($request) => 'done';
});

it('allows request when no bot is detected', function () {
    $this->event->shouldReceive('getBot')->andReturn(BotResult::NOT_DETECTED);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.bot_block')->andReturn(BotBlockConfiguration::BlockAll);

    $response = (new BlockBotsMiddleware($this->event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when a bad bot is detected', function () {
    $this->event->shouldReceive('getBot')->andReturn(BotResult::BAD);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.bot_block')->andReturn(BotBlockConfiguration::BlockBad);

    (new BlockBotsMiddleware($this->event, $this->config))($this->request, $this->next);
})->throws(BotDetectedException::class);

it('blocks request when any bot is detected', function () {
    $this->event->shouldReceive('getBot')->andReturn(BotResult::GOOD);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.bot_block')->andReturn(BotBlockConfiguration::BlockAll);

    (new BlockBotsMiddleware($this->event, $this->config))($this->request, $this->next);
})->throws(BotDetectedException::class);

it('allows request when bad bot is detected but configuration is Allow', function () {
    $this->event->shouldReceive('getBot')->andReturn(BotResult::BAD);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.bot_block')->andReturn(BotBlockConfiguration::Allow);

    $response = (new BlockBotsMiddleware($this->event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});
