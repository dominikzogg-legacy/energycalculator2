<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Orm;

use Chubbyphp\DoctrineDbServiceProvider\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Energycalculator\Model\Day;
use Energycalculator\Model\Comestible;

final class ComestibleWithinDayMapping implements ClassMapMappingInterface
{
    /**
     * @param ClassMetadata $metadata
     *
     * @throws MappingException
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->setPrimaryTable([
            'name' => 'comestible_within_day',
        ]);

        $metadata->mapField([
            'fieldName' => 'id',
            'type' => 'guid',
            'id' => true,
        ]);

        $metadata->mapField([
            'fieldName' => 'createdAt',
            'type' => 'datetime',
        ]);

        $metadata->mapField([
            'fieldName' => 'updatedAt',
            'type' => 'datetime',
            'nullable' => true,
        ]);

        $metadata->mapField([
            'fieldName' => 'sorting',
            'type' => 'smallint',
        ]);

        $metadata->mapField([
            'fieldName' => 'amount',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 4,
        ]);

        $metadata->mapManyToOne([
            'fieldName' => 'day',
            'targetEntity' => Day::class,
            'inversedBy' => 'comestiblesWithinDay',
            'joinTable' => [
                'name' => 'day_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ],
        ]);

        $metadata->mapManyToOne([
            'fieldName' => 'comestible',
            'targetEntity' => Comestible::class,
            'joinTable' => [
                'name' => 'comestible_id',
                'referencedColumnName' => 'id',
            ],
        ]);
    }
}
