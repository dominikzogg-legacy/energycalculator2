<?php

namespace Energycalculator\Deserializer;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
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
     * @return string
     */
    public function getConstructMethod(): string
    {
        return 'create';
    }

    /**
     * @return array
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
