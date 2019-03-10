<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Orm;

use Chubbyphp\DoctrineDbServiceProvider\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\User;

final class DayMapping implements ClassMapMappingInterface
{
    /**
     * @param ClassMetadata $metadata
     *
     * @throws MappingException
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->setPrimaryTable([
            'name' => 'day',
            'uniqueConstraints' => [
                'date_user_id_idx' => ['columns' => ['date', 'user_id']],
            ],
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
            'fieldName' => 'date',
            'type' => 'date',
        ]);

        $metadata->mapField([
            'fieldName' => 'weight',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 4,
            'nullable' => true,
        ]);

        $metadata->mapOneToMany([
            'fieldName' => 'comestiblesWithinDay',
            'targetEntity' => ComestibleWithinDay::class,
            'mappedBy' => 'day',
            'cascade' => ['persist'],
            'orderBy' => [
                'sorting' => 'ASC',
            ],
        ]);

        $metadata->mapManyToOne([
            'fieldName' => 'user',
            'targetEntity' => User::class,
            'joinTable' => [
                'name' => 'user_id',
                'referencedColumnName' => 'id',
            ],
        ]);
    }
}
