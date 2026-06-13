<?php

namespace ErayAydin\Fingerprint\Http\Middleware;

use Closure;
use ErayAydin\Fingerprint\Exceptions\IncognitoModeException;
use Fingerprint\ServerSdk\Model\Event;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * Middleware to block requests from users in incognito mode based on configuration settings.
 */
final readonly class BlockIncognitoMiddleware
{
    /**
     * @var bool Configuration setting that determines if incognito mode users should be blocked.
     */
    private bool $blockIncognito;

    /**
     * @param  Event  $event  The fingerprint request event instance.
     * @param  Repository  $config  The configuration repository.
     */
    public function __construct(
        private Event $event,
        public Repository $config,
    ) {
        $this->blockIncognito = $config->get('fingerprint.middleware.incognito_block');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  Closure  $next  The next middleware in the pipeline.
     *
     * @throws IncognitoModeException
     */
    public function __invoke(Request $request, Closure $next): mixed
    {
        if ($this->blockIncognito && $this->event->getIncognito() === true) {
            throw IncognitoModeException::incognitoModeDetected();
        }

        return $next($request);
    }
}
