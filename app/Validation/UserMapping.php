<?php

namespace Energycalculator\Validation;

use Chubbyphp\Validation\Constraint\EmailConstraint;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Energycalculator\Model\User;

class UserMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return User::class;
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('username', [new NotNullConstraint(), new EmailConstraint()]),
            new PropertyMapping('email', [new NotNullConstraint(), new EmailConstraint()]),
            new PropertyMapping('password', [new NotNullConstraint(), new NotBlankConstraint()]),
            new PropertyMapping('roles', [new NotBlankConstraint()]),
        ];
    }
}
