<?php

namespace Energycalculator\Validation;

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
        return [];
    }
}
