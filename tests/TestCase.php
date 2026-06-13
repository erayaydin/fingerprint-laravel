<?php

namespace ErayAydin\Fingerprint\Tests;

use DateInterval;
use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use ErayAydin\Fingerprint\FingerprintServiceProvider;
use Fingerprint\ServerSdk\Model\BotResult;
use Fingerprint\ServerSdk\Model\Event as SdkEvent;
use Fingerprint\ServerSdk\Model\Identification;
use Fingerprint\ServerSdk\Model\IdentificationConfidence;
use Fingerprint\ServerSdk\Model\IPBlockList;
use Illuminate\Contracts\Config\Repository;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function getSdkEventMock(
        string $eventId = '1708102555327.NLOjmg',
        string $visitorId = 'visitor-id',
        ?bool $incognito = false,
        float $confidence = 0.9,
        int $timestampMs = 1708102555327,
        ?BotResult $bot = BotResult::NOT_DETECTED,
        ?bool $vpn = false,
        ?bool $torNode = false,
    ): SdkEvent {
        $confidenceMock = Mockery::mock(IdentificationConfidence::class);
        $confidenceMock->shouldReceive('getScore')->andReturn($confidence);

        $identificationMock = Mockery::mock(Identification::class);
        $identificationMock->shouldReceive('getVisitorId')->andReturn($visitorId);
        $identificationMock->shouldReceive('getConfidence')->andReturn($confidenceMock);

        $ipBlocklistMock = Mockery::mock(IPBlockList::class);
        $ipBlocklistMock->shouldReceive('getTorNode')->andReturn($torNode);

        $event = Mockery::mock(SdkEvent::class);
        $event->shouldReceive('getEventId')->andReturn($eventId);
        $event->shouldReceive('getIdentification')->andReturn($identificationMock);
        $event->shouldReceive('getIncognito')->andReturn($incognito);
        $event->shouldReceive('getTimestamp')->andReturn($timestampMs);
        $event->shouldReceive('getBot')->andReturn($bot);
        $event->shouldReceive('getVpn')->andReturn($vpn);
        $event->shouldReceive('getIpBlocklist')->andReturn($ipBlocklistMock);

        return $event;
    }

    public function getConfigRepository(?DateInterval $elapsedTime = new DateInterval('P1D')): Repository
    {
        $configRepository = Mockery::mock(Repository::class);
        $configRepository->shouldReceive('get')
            ->with('fingerprint.api_secret')
            ->andReturn('secret');
        $configRepository->shouldReceive('get')
            ->with('fingerprint.region')
            ->andReturn('global');
        $configRepository->shouldReceive('get')
            ->with('fingerprint.event_id_param')
            ->andReturn(null);
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
}
