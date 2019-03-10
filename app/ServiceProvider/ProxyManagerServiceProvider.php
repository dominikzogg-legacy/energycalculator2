<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Doctrine\Common\Persistence\ConnectionRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Energycalculator\Repository\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;

final class ProxyManagerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['proxymanager.factory'] = function () {
            return new LazyLoadingValueHolderFactory();
        };

        $container['proxymanager.doctrine.dbal.connection_registry'] = function () use ($container) {
            return $container['proxymanager.factory']->createProxy(ConnectionRegistry::class,
                function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($container) {
                    $wrappedObject = $container['doctrine.dbal.connection_registry'];
                    $initializer = null;
                }
            );
        };

        $container['proxymanager.doctrine.orm.manager_registry'] = function () use ($container) {
            return $container['proxymanager.factory']->createProxy(ManagerRegistry::class,
                function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($container) {
                    $wrappedObject = $container['doctrine.orm.manager_registry'];
                    $initializer = null;
                }
            );
        };

        $container['proxymanager.security.authentication.passwordmanager'] = function () use ($container) {
            return $container['proxymanager.factory']->createProxy(PasswordManagerInterface::class,
                function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($container) {
                    $wrappedObject = $container['security.authentication.passwordmanager'];
                    $initializer = null;
                }
            );
        };

        $container['proxymanager.'.UserRepository::class] = function () use ($container) {
            return $container['proxymanager.factory']->createProxy(UserRepository::class,
                function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($container) {
                    $wrappedObject = $container[UserRepository::class];
                    $initializer = null;
                }
            );
        };

        $container['proxymanager.validator'] = function () use ($container) {
            return $container['proxymanager.factory']->createProxy(ValidatorInterface::class,
                function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($container) {
                    $wrappedObject = $container['validator'];
                    $initializer = null;
                }
            );
        };
    }
}
