<?php

namespace ErayAydin\Fingerprint\Console;

use Carbon\Carbon;
use Composer\InstalledVersions;
use DateInterval;
use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Console\AboutCommand;

/**
 * Class AboutCommandRegistrar
 *
 * Registers the About command for the Fingerprint Laravel package.
 */
class AboutCommandRegistrar
{
    /**
     * Registers the About command with the given configuration repository.
     *
     * @param  Repository  $configRepository  The configuration repository to access fingerprint config.
     */
    public static function register(Repository $configRepository): void
    {
        if (! class_exists(InstalledVersions::class) || ! class_exists(AboutCommand::class)) {
            return;
        }

        AboutCommand::add('Fingerprint Laravel', fn () => [
            'API Key' => $configRepository->get('fingerprint.api_secret')
                ? ConsoleHelper::success('Configured')
                : ConsoleHelper::error('Not Configured'),
            'Region' => ConsoleHelper::info($configRepository->get('fingerprint.region')),
            'Bot Block' => self::getBotConfigurationDisplay($configRepository->get('fingerprint.middleware.bot_block')),
            'VPN Block' => $configRepository->get('fingerprint.middleware.vpn_block')
                ? ConsoleHelper::success('Enabled')
                : ConsoleHelper::warning('Disabled'),
            'TOR Block' => self::getTorConfigurationDisplay($configRepository->get('fingerprint.middleware.tor_block')),
            'Min. Confidence' => ConsoleHelper::success($configRepository->get('fingerprint.middleware.min_confidence')) ?: ConsoleHelper::warning('Disabled'),
            'Incognito Block' => $configRepository->get('fingerprint.middleware.incognito_block')
                ? ConsoleHelper::success('Enabled')
                : ConsoleHelper::warning('Disabled'),
            'Old Identification' => ConsoleHelper::success(self::getDateIntervalDisplay($configRepository->get('fingerprint.middleware.max_elapsed_time'))),
            'Version' => ConsoleHelper::info(InstalledVersions::getPrettyVersion('erayaydin/fingerprint-laravel')),
        ]);
    }

    /**
     * Returns the bot configuration display text based on the given bot block configuration.
     *
     * @param  BotBlockConfiguration  $botBlock  The bot block configuration.
     * @return string The display text for the bot block configuration.
     */
    private static function getBotConfigurationDisplay(BotBlockConfiguration $botBlock): string
    {
        return match ($botBlock) {
            BotBlockConfiguration::BlockAll => ConsoleHelper::success('Enabled (Good and Bad Bots)'),
            BotBlockConfiguration::BlockBad => ConsoleHelper::success('Enabled (Bad Bots)'),
            BotBlockConfiguration::Allow => ConsoleHelper::warning('Disabled'),
        };
    }

    /**
     * Returns the Tor configuration display text based on the given Tor block configuration.
     *
     * @param  TorBlockConfiguration  $torBlock  The Tor block configuration.
     * @return string The display text for the Tor block configuration.
     */
    private static function getTorConfigurationDisplay(TorBlockConfiguration $torBlock): string
    {
        return match ($torBlock) {
            TorBlockConfiguration::BlockAll => ConsoleHelper::success('Enabled'),
            TorBlockConfiguration::BlockIfSignaled => ConsoleHelper::success('Enabled (if signaled)'),
            TorBlockConfiguration::Allow => ConsoleHelper::warning('Disabled'),
        };
    }

    /**
     * Returns the date interval display text based on the given date interval.
     *
     * @param  DateInterval  $interval  The date interval.
     * @return string The display text for the date interval.
     *
     * @throws Exception
     */
    private static function getDateIntervalDisplay(DateInterval $interval): string
    {
        return Carbon::now()->diffAsCarbonInterval(Carbon::now()->add($interval))->forHumans();
    }
}
