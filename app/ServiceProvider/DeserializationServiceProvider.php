<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use Energycalculator\Collection\DayCollection;
use Energycalculator\Collection\UserCollection;
use Energycalculator\Mapping\Deserialization\ComestibleMapping;
use Energycalculator\Mapping\Deserialization\ComestibleWithinDayMapping;
use Energycalculator\Mapping\Deserialization\DayMapping;
use Energycalculator\Mapping\Deserialization\ComestibleCollectionMapping;
use Energycalculator\Mapping\Deserialization\DayCollectionMapping;
use Energycalculator\Mapping\Deserialization\UserCollectionMapping;
use Energycalculator\Mapping\Deserialization\UserMapping;
use Energycalculator\Mapping\MappingConfig;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Energycalculator\Collection\ComestibleCollection;

final class DeserializationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['deserializer.mappingConfigs'] = [
            UserCollection::class => new MappingConfig(UserCollectionMapping::class),
            User::class => new MappingConfig(UserMapping::class, ['security.authentication.passwordmanager', 'security.authorization.rolehierarchyresolver']),
            DayCollection::class => new MappingConfig(DayCollectionMapping::class),
            Day::class => new MappingConfig(DayMapping::class),
            ComestibleCollection::class => new MappingConfig(ComestibleCollectionMapping::class),
            Comestible::class => new MappingConfig(ComestibleMapping::class),
            ComestibleWithinDay::class => new MappingConfig(ComestibleWithinDayMapping::class, [Repository::class.Comestible::class]),
        ];

        $container['deserializer.denormalizer.objectmappings'] = function () use ($container) {
            $mappings = [];

            foreach ($container['deserializer.mappingConfigs'] as $class => $mappingConfig) {
                $resolver = function () use ($container, $mappingConfig) {
                    $mappingClass = $mappingConfig->getMappingClass();

                    $dependencies = [];
                    foreach ($mappingConfig->getDependencies() as $dependency) {
                        $dependencies[] = $container[$dependency];
                    }

                    return new $mappingClass(...$dependencies);
                };

                $mappings[] = new CallableDenormalizationObjectMapping($class, $resolver);
            }

            return $mappings;
        };
    }
}
