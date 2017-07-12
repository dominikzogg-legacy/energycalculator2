<?php

declare(strict_types=1);

namespace Energycalculator\Validation;

use Chubbyphp\Validation\Constraint\ConstraintInterface;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\ValidationModel\Constraint\ModelReferenceConstraint;
use Energycalculator\Model\ComestibleWithinDay;

final class ComestibleWithinDayMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ComestibleWithinDay::class;
    }

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints(): array
    {
        return [];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('comestible', [new ModelReferenceConstraint(false)]),
            new PropertyMapping('amount', [new NotNullConstraint(), new NumericConstraint()]),
        ];
    }
}
