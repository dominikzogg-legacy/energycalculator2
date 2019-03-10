<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class NegotiationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['negotiator.acceptLanguageNegotiator.values'] = function () use ($container) {
            return $container['locales'];
        };
    }
}
