<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Energycalculator\Middleware\LocaleMiddleware;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class MiddlewareServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[LocaleMiddleware::class] = function () use ($container) {
            return new LocaleMiddleware(
                $container['negotiator.acceptLanguageNegotiator'],
                $container['localeFallback']
            );
        };
    }
}
