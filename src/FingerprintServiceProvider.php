<?php

namespace ErayAydin\Fingerprint;

use ErayAydin\Fingerprint\Console\AboutCommandRegistrar;
use ErayAydin\Fingerprint\Exceptions\InvalidConfiguration;
use ErayAydin\Fingerprint\Http\Middleware\BlockBotsMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockIncognitoMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockOldIdentificationMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockTorMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\BlockVPNMiddleware;
use ErayAydin\Fingerprint\Http\Middleware\MinConfidenceScoreMiddleware;
use Fingerprint\ServerSdk\Model\Event as SdkEvent;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class FingerprintServiceProvider
 *
 * Service provider for the Fingerprint Laravel package.
 */
class FingerprintServiceProvider extends ServiceProvider
{
    /**
     * The list of middlewares with aliases.
     */
    private const MIDDLEWARES = [
        'bots' => BlockBotsMiddleware::class,
        'vpn' => BlockVPNMiddleware::class,
        'tor' => BlockTorMiddleware::class,
        'confidence' => MinConfidenceScoreMiddleware::class,
        'incognito' => BlockIncognitoMiddleware::class,
        'old-identification' => BlockOldIdentificationMiddleware::class,
    ];

    /**
     * Register services and configurations.
     *
     * @throws InvalidConfiguration If the API key is not specified.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fingerprint.php',
            'fingerprint'
        );

        $this->app->singleton(Fingerprint::class, function (Container $app) {
            /** @var Repository $config */
            $config = $app->make(Repository::class);

            /** @var ClientInterface $httpClient */
            $httpClient = $app->make(Client::class);

            $apiKey = $config->get('fingerprint.api_secret');

            if (! $apiKey) {
                throw InvalidConfiguration::apiKeyNotSpecified();
            }

            return new Fingerprint(
                $apiKey,
                $config->get('fingerprint.region'),
                $httpClient,
            );
        });
        $this->app->alias(Fingerprint::class, 'fingerprint');
    }

    /**
     * Boot services and middleware.
     *
     * @throws BindingResolutionException If there is an error resolving bindings.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fingerprint.php' => $this->app->configPath('fingerprint.php'),
            ], 'fingerprint-config');
        }

        $this->app->singleton(SdkEvent::class, function ($app) {
            /** @var Fingerprint $fingerprint */
            $fingerprint = $app->make(Fingerprint::class);

            /** @var Repository $config */
            $config = $app->make(Repository::class);

            $request = $app->make('request');
            $eventId = $this->resolveEventId($request, $config->get('fingerprint.event_id_param'));

            if (! $eventId) {
                return null;
            }

            return $fingerprint->getEvent($eventId);
        });
        $this->app->alias(SdkEvent::class, 'fingerprint.event');

        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $this->registerMiddlewareAliases($router);
        $this->registerMiddlewareGroup($router);

        /** @var Repository $configRepository */
        $configRepository = $this->app->make(Repository::class);

        if (! $this->app->runningUnitTests()) {
            AboutCommandRegistrar::register($configRepository);
        }
    }

    /**
     * Resolve the event ID from the incoming request.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  string|null  $configuredParam  The configured query parameter name, or null to use defaults.
     */
    private function resolveEventId(Request $request, ?string $configuredParam): ?string
    {
        if ($configuredParam !== null) {
            return $request->input($configuredParam) ?: null;
        }

        return $request->input('event_id') ?? $request->input('requestId');
    }

    /**
     * Register middleware aliases.
     *
     * @param  Router  $router  The router instance.
     */
    private function registerMiddlewareAliases(Router $router): void
    {
        foreach (self::MIDDLEWARES as $key => $middleware) {
            $router->aliasMiddleware("fingerprint.$key", $middleware);
        }
    }

    /**
     * Register the fingerprint middleware group.
     *
     * @param  Router  $router  The router instance.
     */
    private function registerMiddlewareGroup(Router $router): void
    {
        $router->middlewareGroup('fingerprint', [
            BlockBotsMiddleware::class,
            BlockVPNMiddleware::class,
            BlockTorMiddleware::class,
            MinConfidenceScoreMiddleware::class,
            BlockIncognitoMiddleware::class,
            BlockOldIdentificationMiddleware::class,
        ]);
    }
}
