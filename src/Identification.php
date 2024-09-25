<?php

namespace ErayAydin\Fingerprint;

use DateTimeImmutable;

/**
 * Class Identification
 *
 * Represents the identification data of an event.
 */
class Identification
{
    /**
     * Identification constructor
     *
     * @param  string  $requestId  The request ID of the event.
     * @param  string  $visitorId  The visitor ID.
     * @param  bool  $incognito  Indicates if the visitor is in incognito mode.
     * @param  DateTimeImmutable  $time  The time of the event.
     * @param  DateTimeImmutable  $timestamp  The timestamp of the event.
     * @param  string  $url  The URL of the event.
     * @param  string  $ip  The IP address of the visitor.
     * @param  float  $confidence  The confidence score of the identification.
     */
    public function __construct(
        public string $requestId,
        public string $visitorId,
        public bool $incognito,
        public DateTimeImmutable $time,
        public DateTimeImmutable $timestamp,
        public string $url,
        public string $ip,
        public float $confidence,
    ) {}
}
