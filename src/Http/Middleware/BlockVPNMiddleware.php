<?php

namespace ErayAydin\Fingerprint\Http\Middleware;

use Closure;
use ErayAydin\Fingerprint\Exceptions\VPNDetectedException;
use Fingerprint\ServerSdk\Model\Event;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * Middleware to block requests from VPN users based on configuration settings.
 */
final readonly class BlockVPNMiddleware
{
    /**
     * @var bool Configuration setting that determines if VPN users should be blocked.
     */
    private bool $blockVPN;

    /**
     * @param  Event  $event  The fingerprint request event instance.
     * @param  Repository  $config  The configuration repository.
     */
    public function __construct(
        private Event $event,
        Repository $config,
    ) {
        $this->blockVPN = $config->get('fingerprint.middleware.vpn_block');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  Closure  $next  The next middleware in the pipeline.
     *
     * @throws VPNDetectedException
     */
    public function __invoke(Request $request, Closure $next): mixed
    {
        if ($this->blockVPN && $this->event->getVpn() === true) {
            throw VPNDetectedException::vpnDetected();
        }

        return $next($request);
    }
}
