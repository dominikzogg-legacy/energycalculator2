<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Energycalculator\Model\Comestible;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;
use Energycalculator\Repository\Repository;
use Energycalculator\Repository\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class RepositoryServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[Repository::class.Comestible::class] = function () use ($container) {
            return new Repository($container['doctrine.orm.em'], Comestible::class);
        };

        $container[Repository::class.Day::class] = function () use ($container) {
            return new Repository($container['doctrine.orm.em'], Day::class);
        };

        $container[Repository::class.User::class] = function () use ($container) {
            return new Repository($container['doctrine.orm.em'], User::class);
        };

        $container[UserRepository::class] = function () use ($container) {
            return new UserRepository($container[Repository::class.User::class]);
        };
    }
}
