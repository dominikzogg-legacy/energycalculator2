<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Energycalculator\Factory\Collection\ComestibleCollectionFactory;
use Energycalculator\Factory\Collection\DayCollectionFactory;
use Energycalculator\Factory\Collection\UserCollectionFactory;
use Energycalculator\Factory\Model\ComestibleFactory;
use Energycalculator\Factory\Model\DayFactory;
use Energycalculator\Factory\Model\UserFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class FactoryServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[ComestibleCollectionFactory::class] = function () {
            return new ComestibleCollectionFactory();
        };

        $container[ComestibleFactory::class] = function () {
            return new ComestibleFactory();
        };

        $container[DayCollectionFactory::class] = function () {
            return new DayCollectionFactory();
        };

        $container[DayFactory::class] = function () {
            return new DayFactory();
        };

        $container[UserCollectionFactory::class] = function () {
            return new UserCollectionFactory();
        };

        $container[UserFactory::class] = function () {
            return new UserFactory();
        };
    }
}
