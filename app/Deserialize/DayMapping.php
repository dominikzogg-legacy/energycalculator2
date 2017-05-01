<?php

namespace Energycalculator\Deserialize;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\DeserializeModel\Deserializer\PropertyModelCollectionDeserializer;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;

class DayMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Day::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [Day::class, 'create'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('date'),
            new PropertyMapping('weight'),
            new PropertyMapping(
                'comestiblesWithinDay',
                new PropertyModelCollectionDeserializer(ComestibleWithinDay::class)
            ),
        ];
    }
}
