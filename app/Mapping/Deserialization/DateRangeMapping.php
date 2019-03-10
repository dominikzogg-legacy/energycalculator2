<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Energycalculator\Model\DateRange;

final class DateRangeMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return DateRange::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [DateRange::class, '__construct'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('from'),
            new PropertyMapping('to'),
        ];
    }
}
