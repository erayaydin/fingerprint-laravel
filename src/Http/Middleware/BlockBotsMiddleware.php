<?php

namespace ErayAydin\Fingerprint\Http\Middleware;

use Closure;
use ErayAydin\Fingerprint\Enums\BotBlockConfiguration;
use ErayAydin\Fingerprint\Exceptions\BotDetectedException;
use Fingerprint\ServerSdk\Model\BotResult;
use Fingerprint\ServerSdk\Model\Event;
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
        $bot = $this->event->getBot();

        if ($this->isBadBotDetected($bot)) {
            throw BotDetectedException::badBotDetected();
        }

        if ($this->isAnyBotDetected($bot)) {
            throw BotDetectedException::botDetected();
        }

        return $next($request);
    }

    /**
     * Check if a bad bot is detected.
     *
     * @param  BotResult|null  $bot  The result of the bot detection.
     */
    private function isBadBotDetected(?BotResult $bot): bool
    {
        if ($this->botBlock !== BotBlockConfiguration::BlockBad) {
            return false;
        }

        return $bot === BotResult::BAD;
    }

    /**
     * Check if any bot is detected.
     *
     * @param  BotResult|null  $bot  The result of the bot detection.
     */
    private function isAnyBotDetected(?BotResult $bot): bool
    {
        if ($this->botBlock !== BotBlockConfiguration::BlockAll) {
            return false;
        }

        return $bot === BotResult::BAD || $bot === BotResult::GOOD;
    }
}
