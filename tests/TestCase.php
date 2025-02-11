<?php

namespace ErayAydin\Fingerprint\Tests;

use DateInterval;
use DateTime;
use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use ErayAydin\Fingerprint\FingerprintServiceProvider;
use Fingerprint\ServerAPI\Model\Botd;
use Fingerprint\ServerAPI\Model\BotdBot;
use Fingerprint\ServerAPI\Model\BotdBotResult;
use Fingerprint\ServerAPI\Model\EventsGetResponse;
use Fingerprint\ServerAPI\Model\Identification;
use Fingerprint\ServerAPI\Model\IdentificationConfidence;
use Fingerprint\ServerAPI\Model\ProductBotd;
use Fingerprint\ServerAPI\Model\ProductIdentification;
use Fingerprint\ServerAPI\Model\Products;
use Fingerprint\ServerAPI\Model\ProductTor;
use Fingerprint\ServerAPI\Model\ProductVPN;
use Fingerprint\ServerAPI\Model\Tor;
use Fingerprint\ServerAPI\Model\VPN;
use Illuminate\Contracts\Config\Repository;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function getEventResponseMock(
        string $requestId = 'request-id',
        string $visitorId = 'visitor-id',
        bool $incognito = false,
        string $url = 'https://example.com',
        string $ip = '127.0.0.1',
        float $confidence = 0.9,
        DateTime $time = new DateTime,
        BotdBotResult $botDResult = BotdBotResult::NOT_DETECTED,
        bool $isTor = false,
        bool $isVPN = false,
    ): EventsGetResponse {
        $productsResponse = Mockery::mock(Products::class);
        $productsResponse->shouldReceive('getIdentification')->andReturn($this->getIdentificationMock($requestId, $visitorId, $incognito, $url, $ip, $confidence, $time));
        $productsResponse->shouldReceive('getBotd')->andReturn($this->getBotDResponseMock($botDResult));
        $productsResponse->shouldReceive('getTor')->andReturn($this->getTorResponseMock($isTor));
        $productsResponse->shouldReceive('getVpn')->andReturn($this->getVpnResponseMock($isVPN));

        $eventResponse = Mockery::mock(EventsGetResponse::class);
        $eventResponse->shouldReceive('getProducts')->andReturn($productsResponse);

        return $eventResponse;
    }

    public function getConfigRepository(?DateInterval $elapsedTime = new DateInterval('P1D')): Repository
    {
        $configRepository = Mockery::mock(Repository::class);
        $configRepository->shouldReceive('get')
            ->with('fingerprint.api_secret')
            ->andReturn('secret');
        $configRepository->shouldReceive('get')
            ->with('fingerprint.region')
            ->andReturn('eu');
        $configRepository->shouldReceive('get')
            ->with('fingerprint.middleware.bot_block')
            ->andReturn(BotBlockConfiguration::BlockBad);
        $configRepository->shouldReceive('get')
            ->with('fingerprint.middleware.vpn_block')
            ->andReturn(true);
        $configRepository->shouldReceive('get')
            ->with('fingerprint.middleware.tor_block')
            ->andReturn(TorBlockConfiguration::BlockIfSignaled);
        $configRepository->shouldReceive('get')
            ->with('fingerprint.middleware.min_confidence')
            ->andReturn(0.8);
        $configRepository->shouldReceive('get')
            ->with('fingerprint.middleware.incognito_block')
            ->andReturn(true);
        $configRepository->shouldReceive('get')
            ->with('fingerprint.middleware.max_elapsed_time')
            ->andReturn($elapsedTime);

        return $configRepository;
    }

    protected function getPackageProviders($app): array
    {
        return [
            FingerprintServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }

    private function getIdentificationMock(
        string $requestId,
        string $visitorId,
        bool $incognito,
        string $url,
        string $ip,
        float $confidence,
        DateTime $time,
    ): ProductIdentification {
        $confidenceMock = Mockery::mock(IdentificationConfidence::class);
        $confidenceMock->shouldReceive('getScore')->andReturn($confidence);

        $identificationData = Mockery::mock(Identification::class);

        $identificationData->shouldReceive('getTimestamp')->andReturn(1000000000000);
        $identificationData->shouldReceive('getRequestId')->andReturn($requestId);
        $identificationData->shouldReceive('getVisitorId')->andReturn($visitorId);
        $identificationData->shouldReceive('getIncognito')->andReturn($incognito);
        $identificationData->shouldReceive('getTime')->andReturn($time);
        $identificationData->shouldReceive('getUrl')->andReturn($url);
        $identificationData->shouldReceive('getIp')->andReturn($ip);
        $identificationData->shouldReceive('getConfidence')->andReturn($confidenceMock);

        $identification = Mockery::mock(ProductIdentification::class);

        $identification->shouldReceive('getData')->andReturn($identificationData);

        return $identification;
    }

    private function getBotDResponseMock(BotdBotResult $botDResult): ProductBotd
    {
        $botDDetectionResult = Mockery::mock(BotdBot::class);
        $botDDetectionResult->shouldReceive('getResult')->andReturn($botDResult);

        $botDResultMock = Mockery::mock(Botd::class);
        $botDResultMock->shouldReceive('getBot')->andReturn($botDDetectionResult);

        $botDDetection = Mockery::mock(ProductBotd::class);
        $botDDetection->shouldReceive('getData')->andReturn($botDResultMock);

        return $botDDetection;
    }

    private function getTorResponseMock(bool $isTor): ProductTor
    {
        $torResult = Mockery::mock(Tor::class);
        $torResult->shouldReceive('getResult')->andReturn($isTor);

        $torResponse = Mockery::mock(ProductTor::class);
        $torResponse->shouldReceive('getData')->andReturn($torResult);

        return $torResponse;
    }

    private function getVpnResponseMock(bool $isVPN): ProductVPN
    {
        $vpnResult = Mockery::mock(VPN::class);
        $vpnResult->shouldReceive('getResult')->andReturn($isVPN);

        $vpnResponse = Mockery::mock(ProductVPN::class);
        $vpnResponse->shouldReceive('getData')->andReturn($vpnResult);

        return $vpnResponse;
    }
}
