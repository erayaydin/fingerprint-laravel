<?php

use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\RegionNotSupportedException;
use ErayAydin\Fingerprint\Fingerprint;
use Fingerprint\ServerAPI\Api\FingerprintApi;
use Fingerprint\ServerAPI\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->apiKey = 'api-key';
    $this->region = 'global';
    $this->responseJson = File::get(__DIR__.'/../Fixtures/response.json');
    $this->httpClient = new Client;
});

it('creates a Fingerprint instance with default HTTP client', function () {
    $fingerprint = new Fingerprint($this->apiKey, $this->region);
    expect($fingerprint->client)->toBeInstanceOf(FingerprintApi::class);
});

it('creates a Fingerprint instance with provided HTTP client', function () {
    $fingerprint = new Fingerprint($this->apiKey, $this->region, $this->httpClient);
    expect($fingerprint->client)->toBeInstanceOf(FingerprintApi::class);
});

it('retrieves an event by request ID', function () {
    $handlerStack = HandlerStack::create(new MockHandler([
        new Response(200, [], $this->responseJson),
    ]));
    $httpClient = new Client([
        'handler' => $handlerStack,
    ]);
    $fingerprint = new Fingerprint($this->apiKey, $this->region, $httpClient);

    $event = $fingerprint->getEvent('request-id');
    expect($event)->toBeInstanceOf(Event::class);
});

it('throws RegionNotSupportedException for unsupported region', function () {
    $unsupportedRegion = 'unsupported_region';
    $this->expectException(RegionNotSupportedException::class);
    new Fingerprint($this->apiKey, $unsupportedRegion);
});

it('throws ApiException when Fingerprint API error occurs', function () {
    $handlerStack = HandlerStack::create(new MockHandler([
        new Response(500, [], null),
    ]));
    $httpClient = new Client([
        'handler' => $handlerStack,
    ]);
    $fingerprint = new Fingerprint($this->apiKey, $this->region, $httpClient);
    $this->expectException(ApiException::class);
    $fingerprint->getEvent('request-id');
});
