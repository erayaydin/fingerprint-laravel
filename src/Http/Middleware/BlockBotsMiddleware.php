<?php

namespace ErayAydin\Fingerprint\Http\Middleware;

use Closure;
use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Enums\BotDResult;
use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\BotDetectedException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * Middleware to block bot requests based on configuration settings.
 */
final readonly class BlockBotsMiddleware
{
    /**
     * @var BotBlockConfiguration Configuration setting that determines the bot blocking behavior.
     */
    private BotBlockConfiguration $botBlock;

    /**
     * @param  Event  $event  The fingerprint request event instance.
     * @param  Repository  $config  The configuration repository.
     */
    public function __construct(
        private Event $event,
        public Repository $config,
    ) {
        $this->botBlock = $config->get('fingerprint.middleware.bot_block');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  Closure  $next  The next middleware in the pipeline.
     *
     * @throws BotDetectedException
     */
    public function __invoke(Request $request, Closure $next): mixed
    {
        $botDResult = $this->event->botD;

        if ($this->isBadBotDetected($botDResult)) {
            throw BotDetectedException::badBotDetected();
        }

        if ($this->isAnyBotDetected($botDResult)) {
            throw BotDetectedException::botDetected();
        }

        return $next($request);
    }

    /**
     * Check if a bad bot is detected.
     *
     * @param  BotDResult  $result  The result of the bot detection.
     */
    private function isBadBotDetected(BotDResult $result): bool
    {
        if ($this->botBlock != BotBlockConfiguration::BlockBad) {
            return false;
        }

        return $result == BotDResult::Bad;
    }

    /**
     * Check if any bot is detected.
     *
     * @param  BotDResult  $result  The result of the bot detection.
     */
    private function isAnyBotDetected(BotDResult $result): bool
    {
        if ($this->botBlock != BotBlockConfiguration::BlockAll) {
            return false;
        }

        return $result == BotDResult::Bad || $result == BotDResult::Good;
    }
}
