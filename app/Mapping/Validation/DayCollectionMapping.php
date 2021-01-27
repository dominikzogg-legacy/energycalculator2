<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Validation;

use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Constraint\NumericRangeConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;
use Energycalculator\Collection\DayCollection;
use Energycalculator\Mapping\Validation\Constraint\SortConstraint;

final class DayCollectionMapping implements ValidationMappingProviderInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return DayCollection::class;
    }

    /**
     * @param string $path
     *
     * @return ValidationClassMappingInterface
     */
    public function getValidationClassMapping(string $path): ValidationClassMappingInterface
    {
        return ValidationClassMappingBuilder::create([])->getMapping();
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return ValidationPropertyMappingInterface[]
     */
    public function getValidationPropertyMappings(string $path, string $type = null): array
    {
        return [
            ValidationPropertyMappingBuilder::create('page', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(1),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('perPage', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(1),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('sort', [
                new SortConstraint(['date']),
            ])->getMapping(),
        ];
    }
}
