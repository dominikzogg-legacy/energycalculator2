<?php

namespace Energycalculator\Validation;

use Chubbyphp\Validation\Constraint\DateConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\ValidationModel\Constraint\ModelCollectionConstraint;
use Chubbyphp\ValidationModel\Constraint\ModelReferenceConstraint;
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
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('user', [new ModelReferenceConstraint()]),
            new PropertyMapping('date', [new NotNullConstraint(), new DateConstraint()]),
            new PropertyMapping('weight', [new NumericConstraint()]),
            new PropertyMapping('comestibleWithinDays', [new ModelCollectionConstraint()]),
        ];
    }
}
