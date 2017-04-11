<?php

namespace Energycalculator\Validation;

use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Chubbyphp\ValidationModel\Constraint\ModelReferenceConstraint;
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
        return [
            new PropertyMapping('user', [new ModelReferenceConstraint()]),
            new PropertyMapping('name', [new NotNullConstraint(), new NotBlankConstraint()]),
            new PropertyMapping('calorie', [new NotNullConstraint(), new NumericConstraint()]),
            new PropertyMapping('protein', [new NotNullConstraint(), new NumericConstraint()]),
            new PropertyMapping('carbohydrate', [new NotNullConstraint(), new NumericConstraint()]),
            new PropertyMapping('fat', [new NotNullConstraint(), new NumericConstraint()]),
            new PropertyMapping('defaultValue', [new NumericConstraint()]),
        ];
    }
}
