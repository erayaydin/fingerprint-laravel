<?php

namespace ErayAydin\Fingerprint;

use ErayAydin\Fingerprint\Exceptions\RegionNotSupportedException;
use Fingerprint\ServerAPI\Api\FingerprintApi;
use Fingerprint\ServerAPI\ApiException;
use Fingerprint\ServerAPI\Configuration;
use Fingerprint\ServerAPI\SerializationException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;

/**
 * Class Fingerprint
 *
 * Provides methods to interact with the Fingerprint API.
 */
class Fingerprint
{
    /**
     * @var FingerprintApi The Fingerprint API client.
     */
    public FingerprintApi $client;

    /**
     * Fingerprint constructor.
     *
     * @param  string  $apiKey  The API key for authentication.
     * @param  string  $region  The region for the API.
     * @param  ClientInterface|null  $httpClient  The HTTP client instance.
     */
    public function __construct(string $apiKey, string $region, ?ClientInterface $httpClient = null)
    {
        $config = Configuration::getDefaultConfiguration($apiKey, Fingerprint::getRegion($region));

        if ($httpClient === null) {
            $httpClient = new Client;
        }

        $this->client = new FingerprintApi($httpClient, $config);
    }

    /**
     * Retrieves an event by request ID.
     *
     * @param  string  $requestId  The request ID of the event.
     * @return Event The event instance.
     *
     * @throws ApiException If there is a Fingerprint API error.
     * @throws GuzzleException If there is an HTTP client error.
     * @throws SerializationException If there is a serialization error.
     */
    public function getEvent(string $requestId): Event
    {
        $model = Arr::first($this->client->getEvent($requestId));

        return Event::createFromEventResponse($model);
    }

    /**
     * Gets the region code based on the region name.
     *
     * @param  string  $region  The region name.
     * @return string The region code.
     *
     * @throws RegionNotSupportedException If the region is not supported.
     */
    private static function getRegion(string $region): string
    {
        return match ($region) {
            'global' => Configuration::REGION_GLOBAL,
            'eu', 'europe' => Configuration::REGION_EUROPE,
            'ap', 'asia' => Configuration::REGION_ASIA,
            default => throw RegionNotSupportedException::regionNotSupported($region),
        };
    }
}
