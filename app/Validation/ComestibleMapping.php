<?php

namespace Energycalculator\Validation;

use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Energycalculator\Model\Comestible;

class ComestibleMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Comestible::class;
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [];
    }
}
