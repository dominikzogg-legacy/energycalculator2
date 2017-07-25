<?php

declare(strict_types=1);

namespace Energycalculator\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Energycalculator\Search\ComestibleSearch;

final class ComestibleSearchMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ComestibleSearch::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [ComestibleSearch::class, 'create'];
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
