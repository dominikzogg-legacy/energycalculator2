<?php

declare(strict_types=1);

namespace Energycalculator\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Energycalculator\Search\DaySearch;

final class DaySearchMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return DaySearch::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [DaySearch::class, 'create'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('page'),
            new PropertyMapping('perPage'),
            new PropertyMapping('sort'),
            new PropertyMapping('order'),
        ];
    }
}
