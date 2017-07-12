<?php

declare(strict_types=1);

namespace Energycalculator\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Chubbyphp\DeserializationModel\Deserializer\PropertyModelCollectionDeserializer;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;

final class DayMapping implements ObjectMappingInterface
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
