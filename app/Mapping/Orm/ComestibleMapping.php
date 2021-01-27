<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Orm;

use Chubbyphp\DoctrineDbServiceProvider\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Energycalculator\Model\User;

final class ComestibleMapping implements ClassMapMappingInterface
{
    /**
     * @param ClassMetadata $metadata
     *
     * @throws MappingException
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->setPrimaryTable([
            'name' => 'comestible',
            'uniqueConstraints' => [
                'user_idx' => ['columns' => ['user_id']],
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
            'fieldName' => 'name',
            'type' => 'string',
        ]);

        $metadata->mapField([
            'fieldName' => 'calorie',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 4,
        ]);

        $metadata->mapField([
            'fieldName' => 'protein',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 4,
        ]);

        $metadata->mapField([
            'fieldName' => 'carbohydrate',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 4,
        ]);

        $metadata->mapField([
            'fieldName' => 'fat',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 4,
        ]);

        $metadata->mapField([
            'fieldName' => 'defaultValue',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 4,
            'nullable' => true,
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
