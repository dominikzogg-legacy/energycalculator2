<?php

namespace Energycalculator\Validation;

use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\ValidationModel\Constraint\ModelReferenceConstraint;
use Energycalculator\Model\ComestibleWithinDay;

class ComestibleWithinDayMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ComestibleWithinDay::class;
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('comestible', [new ModelReferenceConstraint()]),
            new PropertyMapping('amount', [new NotNullConstraint(), new NumericConstraint()]),
        ];
    }
}
