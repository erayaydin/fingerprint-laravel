<?php

namespace ErayAydin\Fingerprint\Tests;

use DateInterval;
use DateTime;
use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use ErayAydin\Fingerprint\FingerprintServiceProvider;
use Fingerprint\ServerAPI\Model\BotdDetectionResult;
use Fingerprint\ServerAPI\Model\BotdResult;
use Fingerprint\ServerAPI\Model\Confidence;
use Fingerprint\ServerAPI\Model\EventResponse;
use Fingerprint\ServerAPI\Model\ProductsResponse;
use Fingerprint\ServerAPI\Model\ProductsResponseBotd;
use Fingerprint\ServerAPI\Model\ProductsResponseIdentification;
use Fingerprint\ServerAPI\Model\ProductsResponseIdentificationData;
use Fingerprint\ServerAPI\Model\SignalResponseTor;
use Fingerprint\ServerAPI\Model\SignalResponseVpn;
use Fingerprint\ServerAPI\Model\TorResult;
use Fingerprint\ServerAPI\Model\VpnResult;
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
        string $botDResult = 'notDetected',
        bool $isTor = false,
        bool $isVPN = false,
    ): EventResponse {
        $productsResponse = Mockery::mock(ProductsResponse::class);
        $productsResponse->shouldReceive('getIdentification')->andReturn($this->getIdentificationMock($requestId, $visitorId, $incognito, $url, $ip, $confidence, $time));
        $productsResponse->shouldReceive('getBotd')->andReturn($this->getBotDResponseMock($botDResult));
        $productsResponse->shouldReceive('getTor')->andReturn($this->getTorResponseMock($isTor));
        $productsResponse->shouldReceive('getVpn')->andReturn($this->getVpnResponseMock($isVPN));

        $eventResponse = Mockery::mock(EventResponse::class);
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
    ): ProductsResponseIdentification {
        $confidenceMock = Mockery::mock(Confidence::class);
        $confidenceMock->shouldReceive('getScore')->andReturn($confidence);

        $identificationData = Mockery::mock(ProductsResponseIdentificationData::class);

        $identificationData->shouldReceive('getTimestamp')->andReturn(1000000000000);
        $identificationData->shouldReceive('getRequestId')->andReturn($requestId);
        $identificationData->shouldReceive('getVisitorId')->andReturn($visitorId);
        $identificationData->shouldReceive('getIncognito')->andReturn($incognito);
        $identificationData->shouldReceive('getTime')->andReturn($time);
        $identificationData->shouldReceive('getUrl')->andReturn($url);
        $identificationData->shouldReceive('getIp')->andReturn($ip);
        $identificationData->shouldReceive('getConfidence')->andReturn($confidenceMock);

        $identification = Mockery::mock(ProductsResponseIdentification::class);

        $identification->shouldReceive('getData')->andReturn($identificationData);

        return $identification;
    }

    private function getBotDResponseMock(string $botDResult): ProductsResponseBotd
    {
        $botDDetectionResult = Mockery::mock(BotdDetectionResult::class);
        $botDDetectionResult->shouldReceive('getResult')->andReturn($botDResult);

        $botDResultMock = Mockery::mock(BotdResult::class);
        $botDResultMock->shouldReceive('getBot')->andReturn($botDDetectionResult);

        $botDDetection = Mockery::mock(ProductsResponseBotd::class);
        $botDDetection->shouldReceive('getData')->andReturn($botDResultMock);

        return $botDDetection;
    }

    private function getTorResponseMock(bool $isTor): SignalResponseTor
    {
        $torResult = Mockery::mock(TorResult::class);
        $torResult->shouldReceive('getResult')->andReturn($isTor);

        $torResponse = Mockery::mock(SignalResponseTor::class);
        $torResponse->shouldReceive('getData')->andReturn($torResult);

        return $torResponse;
    }

    private function getVpnResponseMock(bool $isVPN): SignalResponseVpn
    {
        $vpnResult = Mockery::mock(VpnResult::class);
        $vpnResult->shouldReceive('getResult')->andReturn($isVPN);

        $vpnResponse = Mockery::mock(SignalResponseVpn::class);
        $vpnResponse->shouldReceive('getData')->andReturn($vpnResult);

        return $vpnResponse;
    }
}
