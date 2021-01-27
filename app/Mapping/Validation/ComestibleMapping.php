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
use Energycalculator\Model\Comestible;
use Chubbyphp\Validation\Constraint\Doctrine\UniqueConstraint;
use Doctrine\Common\Persistence\ObjectManager;

final class ComestibleMapping implements ValidationMappingProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return Comestible::class;
    }

    /**
     * @param string $path
     *
     * @return ValidationClassMappingInterface|null
     */
    public function getValidationClassMapping(string $path)
    {
        return ValidationClassMappingBuilder::create([
            new UniqueConstraint($this->objectManager, ['name', 'user']),
        ])->getMapping();
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
            ValidationPropertyMappingBuilder::create('user', [
                new NotNullConstraint(),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('name', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('calorie', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(0),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('protein', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(0),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('carbohydrate', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(0),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('fat', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new NumericConstraint(),
                new NumericRangeConstraint(0),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('defaultValue', [
                new NumericConstraint(),
                new NumericRangeConstraint(0),
            ])->getMapping(),
        ];
    }
}
