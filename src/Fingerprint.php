<?php

namespace ErayAydin\Fingerprint;

use DateMalformedStringException;
use ErayAydin\Fingerprint\Exceptions\RegionNotSupportedException;
use Fingerprint\ServerSdk\Api\FingerprintApi;
use Fingerprint\ServerSdk\ApiException;
use Fingerprint\ServerSdk\Configuration;
use Fingerprint\ServerSdk\Model\Event;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

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
        $config = new Configuration($apiKey, Fingerprint::getRegion($region));

        $this->client = new FingerprintApi($config, $httpClient);
    }

    /**
     * Retrieves an event by event ID.
     *
     * @param  string  $eventId  The event ID to look up.
     * @return Event The event instance.
     *
     * @throws ApiException If there is a Fingerprint API error.
     * @throws GuzzleException If there is an HTTP client error.
     * @throws DateMalformedStringException
     */
    public function getEvent(string $eventId): Event
    {
        return $this->client->getEvent($eventId);
    }

    /**
     * Gets the region URL based on the region name.
     *
     * @param  string  $region  The region name.
     * @return string The region URL.
     *
     * @throws RegionNotSupportedException If the region is not supported.
     */
    private static function getRegion(string $region): string
    {
        return match ($region) {
            'global', 'us' => Configuration::REGION_GLOBAL,
            'eu', 'europe' => Configuration::REGION_EUROPE,
            'ap', 'asia' => Configuration::REGION_ASIA,
            default => throw RegionNotSupportedException::regionNotSupported($region),
        };
    }
}
