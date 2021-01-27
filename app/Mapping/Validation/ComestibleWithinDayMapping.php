<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Validation;

use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Constraint\NumericRangeConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;
use Energycalculator\Model\ComestibleWithinDay;

final class ComestibleWithinDayMapping implements ValidationMappingProviderInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ComestibleWithinDay::class;
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
            ValidationPropertyMappingBuilder::create('comestible', [
                new NotNullConstraint(),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('amount', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(0),
            ])->getMapping(),
        ];
    }
}
