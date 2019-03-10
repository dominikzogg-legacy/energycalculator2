<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Chubbyphp\Validation\Mapping\CallableValidationMappingProvider;
use Energycalculator\Collection\ComestibleCollection;
use Energycalculator\Collection\DayCollection;
use Energycalculator\Collection\UserCollection;
use Energycalculator\Mapping\MappingConfig;
use Energycalculator\Mapping\Validation\ComestibleCollectionMapping;
use Energycalculator\Mapping\Validation\ComestibleMapping;
use Energycalculator\Mapping\Validation\ComestibleWithinDayMapping;
use Energycalculator\Mapping\Validation\DayCollectionMapping;
use Energycalculator\Mapping\Validation\DayMapping;
use Energycalculator\Mapping\Validation\UserCollectionMapping;
use Energycalculator\Mapping\Validation\UserMapping;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ValidationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['validator.mappingConfigs'] = [
            Comestible::class => new MappingConfig(ComestibleMapping::class, ['doctrine.orm.em']),
            ComestibleCollection::class => new MappingConfig(ComestibleCollectionMapping::class),
            ComestibleWithinDay::class => new MappingConfig(ComestibleWithinDayMapping::class),
            Day::class => new MappingConfig(DayMapping::class, ['doctrine.orm.em']),
            DayCollection::class => new MappingConfig(DayCollectionMapping::class),
            User::class => new MappingConfig(UserMapping::class, ['doctrine.orm.em']),
            UserCollection::class => new MappingConfig(UserCollectionMapping::class),
        ];

        $container['validator.mappings'] = function () use ($container) {
            $mappings = [];

            foreach ($container['validator.mappingConfigs'] as $class => $mappingConfig) {
                $resolver = function () use ($container, $mappingConfig) {
                    $mappingClass = $mappingConfig->getMappingClass();

                    $dependencies = [];
                    foreach ($mappingConfig->getDependencies() as $dependency) {
                        $dependencies[] = $container[$dependency];
                    }

                    return new $mappingClass(...$dependencies);
                };

                $mappings[] = new CallableValidationMappingProvider($class, $resolver);
            }

            return $mappings;
        };
    }
}
