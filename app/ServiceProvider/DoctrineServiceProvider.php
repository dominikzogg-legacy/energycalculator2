<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Energycalculator\Mapping\Orm\ComestibleMapping;
use Energycalculator\Mapping\Orm\ComestibleWithinDayMapping;
use Energycalculator\Mapping\Orm\DayMapping;
use Energycalculator\Mapping\Orm\UserMapping;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class DoctrineServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['doctrine.orm.em.options'] = [
            'mappings' => [
                [
                    'type' => 'class_map',
                    'namespace' => 'Energycalculator\Model',
                    'map' => [
                        Comestible::class => ComestibleMapping::class,
                        ComestibleWithinDay::class => ComestibleWithinDayMapping::class,
                        Day::class => DayMapping::class,
                        User::class => UserMapping::class,
                    ],
                ],
            ],
        ];
    }
}
