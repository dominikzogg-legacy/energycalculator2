<?php

declare(strict_types=1);

namespace Energycalculator\Validation;

use Chubbyphp\Validation\Constraint\ChoiceConstraint;
use Chubbyphp\Validation\Constraint\ConstraintInterface;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Constraint\NumericRangeConstraint;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
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
            new PropertyMapping('page', [
                new NotNullConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(1),
            ]),
            new PropertyMapping('perPage', [
                new NotNullConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(1),
            ]),
            new PropertyMapping('sort', [
                new NotNullConstraint(),
                new ChoiceConstraint(ChoiceConstraint::TYPE_STRING, [ComestibleSearch::SORT_NAME]),
            ]),
            new PropertyMapping('order', [
                new NotNullConstraint(),
                new ChoiceConstraint(ChoiceConstraint::TYPE_STRING, [
                    ComestibleSearch::ORDER_ASC,
                    ComestibleSearch::ORDER_DESC, ]
                ),
            ]),
        ];
    }
}
