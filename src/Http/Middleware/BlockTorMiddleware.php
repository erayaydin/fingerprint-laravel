<?php

namespace ErayAydin\Fingerprint\Http\Middleware;

use Closure;
use ErayAydin\Fingerprint\Enums\TorBlockConfiguration;
use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\TorDetectionException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * Middleware to block requests from Tor network users based on configuration settings.
 */
final readonly class BlockTorMiddleware
{
    /**
     * @var TorBlockConfiguration Configuration setting that determines the Tor blocking behavior.
     */
    private TorBlockConfiguration $torBlock;

    /**
     * @param  Event  $event  The fingerprint request event instance.
     * @param  Repository  $config  The configuration repository.
     */
    public function __construct(
        private Event $event,
        public Repository $config,
    ) {
        $this->torBlock = $config->get('fingerprint.middleware.tor_block');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  Closure  $next  The next middleware in the pipeline.
     *
     * @throws TorDetectionException
     */
    public function __invoke(Request $request, Closure $next): mixed
    {
        $tor = $this->event->isTor;

        if ($this->torBlock === TorBlockConfiguration::BlockAll && $tor !== false) {
            throw TorDetectionException::torDetectionRequired();
        }

        if ($this->torBlock === TorBlockConfiguration::BlockIfSignaled && $tor === true) {
            throw TorDetectionException::torDetected();
        }

        return $next($request);
    }
}
