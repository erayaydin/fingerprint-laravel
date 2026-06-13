<?php

use ErayAydin\Fingerprint\Exceptions\MinConfidenceScoreException;
use ErayAydin\Fingerprint\Http\Middleware\MinConfidenceScoreMiddleware;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->config = Mockery::mock(Repository::class);
    $this->request = Mockery::mock(Request::class);
    $this->next = fn ($request) => 'done';
});

it('allows request when confidence score meets the minimum requirement', function () {
    $event = $this->getSdkEventMock(confidence: 0.8);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.min_confidence')->andReturn(0.7);

    $response = (new MinConfidenceScoreMiddleware($event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});

it('blocks request when confidence score is below the minimum requirement', function () {
    $event = $this->getSdkEventMock(confidence: 0.5);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.min_confidence')->andReturn(0.7);

    (new MinConfidenceScoreMiddleware($event, $this->config))($this->request, $this->next);
})->throws(MinConfidenceScoreException::class);

it('allows request when no minimum confidence score is set', function () {
    $event = $this->getSdkEventMock(confidence: 0.5);
    $this->config->shouldReceive('get')->with('fingerprint.middleware.min_confidence')->andReturn(null);

    $response = (new MinConfidenceScoreMiddleware($event, $this->config))($this->request, $this->next);

    expect($response)->toBe('done');
});
