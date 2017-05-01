<?php

namespace Energycalculator\Deserialize;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
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
     * @return callable
     */
    public function getFactory(): callable
    {
        return [Comestible::class, 'create'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('name'),
            new PropertyMapping('calorie'),
            new PropertyMapping('protein'),
            new PropertyMapping('carbohydrate'),
            new PropertyMapping('fat'),
            new PropertyMapping('defaultValue'),
        ];
    }
}
