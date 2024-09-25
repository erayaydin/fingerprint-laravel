<?php

namespace ErayAydin\Fingerprint\Http\Middleware;

use Closure;
use ErayAydin\Fingerprint\Event;
use ErayAydin\Fingerprint\Exceptions\MinConfidenceScoreException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * Middleware to block requests with a confidence score lower than the minimum required score based on configuration
 * settings.
 */
final readonly class MinConfidenceScoreMiddleware
{
    /**
     * @var float|null Configuration setting that determines the minimum required confidence score.
     */
    private ?float $minConfidenceScore;

    /**
     * @param  Event  $event  The fingerprint request event instance.
     * @param  Repository  $config  The configuration repository.
     */
    public function __construct(
        private Event $event,
        Repository $config,
    ) {
        $this->minConfidenceScore = $config->get('fingerprint.middleware.min_confidence');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  Closure  $next  The next middleware in the pipeline.
     */
    public function __invoke(Request $request, Closure $next): mixed
    {
        $confidenceScore = $this->event->identification->confidence;

        if ($this->minConfidenceScore !== null && $confidenceScore < $this->minConfidenceScore) {
            throw MinConfidenceScoreException::minConfidenceScoreNotReached($confidenceScore, $this->minConfidenceScore);
        }

        return $next($request);
    }
}
