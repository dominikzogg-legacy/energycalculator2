<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Orm;

use Chubbyphp\DoctrineDbServiceProvider\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class UserMapping implements ClassMapMappingInterface
{
    /**
     * @param ClassMetadata $metadata
     *
     * @throws MappingException
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->setPrimaryTable([
            'name' => 'user',
            'uniqueConstraints' => [
                'email_idx' => ['email'],
            ],
        ]);

        $metadata->setPrimaryTable([
            'name' => 'user',
            'uniqueConstraints' => [
                'email_idx' => ['columns' => ['email']],
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
            'fieldName' => 'email',
            'type' => 'string',
        ]);

        $metadata->mapField([
            'fieldName' => 'password',
            'type' => 'string',
        ]);

        $metadata->mapField([
            'fieldName' => 'roles',
            'type' => 'json_array',
        ]);
    }
}
