<?php

namespace ErayAydin\Fingerprint\Http\Middleware;

use Carbon\Carbon;
use Closure;
use DateInterval;
use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\OldIdentificationException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * Middleware to block requests with old identification timestamps based on configuration settings.
 */
final readonly class BlockOldIdentificationMiddleware
{
    /**
     * @var DateInterval Configuration setting that determines the maximum allowed elapsed time for identification.
     */
    private DateInterval $maxElapsedTime;

    /**
     * @param  Event  $event  The fingerprint request event instance.
     * @param  Repository  $config  The configuration repository.
     */
    public function __construct(
        private Event $event,
        public Repository $config,
    ) {
        $this->maxElapsedTime = $config->get('fingerprint.middleware.max_elapsed_time');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  Closure  $next  The next middleware in the pipeline.
     *
     * @throws OldIdentificationException
     */
    public function __invoke(Request $request, Closure $next): mixed
    {
        $time = Carbon::createFromImmutable($this->event->identification->time);

        if (Carbon::now()->sub($this->maxElapsedTime)->greaterThan($time)) {
            throw OldIdentificationException::oldIdentification();
        }

        return $next($request);
    }
}
