<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Security\Authorization\RoleAuthorization;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Repository\UserRepository;
use Energycalculator\Security\AuthenticationErrorHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class SecurityServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[FormAuthentication::class] = function ($container) {
            return new FormAuthentication(
                $container['security.authentication.passwordmanager'],
                $container['session'],
                $container[UserRepository::class],
                $container['logger']
            );
        };

        $container[RoleAuthorization::class] = function ($container) {
            return new RoleAuthorization(
                $container['security.authorization.rolehierarchyresolver'],
                $container['logger']
            );
        };

        $container['security.authentication.errorResponseHandler'] = function () use ($container) {
            return new AuthenticationErrorHandler($container[ErrorResponseHandler::class]);
        };

        $container->extend('security.authentication.authentications', function (array $authentications) use ($container) {
            $authentications[] = $container[FormAuthentication::class];

            return $authentications;
        });

        $container->extend('security.authorization.authorizations', function (array $authorizations) use ($container) {
            $authorizations[] = $container[RoleAuthorization::class];

            return $authorizations;
        });

        $container->extend('security.authorization.rolehierarchy', function (array $rolehierarchy) use ($container) {
            $rolehierarchy['ADMIN'] = ['USER'];
            $rolehierarchy['USER'] = ['COMESTIBLE', 'DAY'];
            $rolehierarchy['COMESTIBLE'] = [
                'COMESTIBLE_LIST',
                'COMESTIBLE_READ',
                'COMESTIBLE_CREATE',
                'COMESTIBLE_UPDATE',
                'COMESTIBLE_DELETE',
            ];

            $rolehierarchy['DAY'] = [
                'DAY_LIST',
                'DAY_READ',
                'DAY_CREATE',
                'DAY_UPDATE',
                'DAY_DELETE',
                'CHART_WEIGHT',
                'CHART_CALORIE',
                'CHART_ENERGYMIX',
            ];

            return $rolehierarchy;
        });
    }
}
