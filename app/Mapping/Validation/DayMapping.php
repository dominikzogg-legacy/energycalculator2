<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Validation;

use Chubbyphp\Validation\Constraint\DateTimeConstraint;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Constraint\NumericRangeConstraint;
use Chubbyphp\Validation\Constraint\ValidConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Energycalculator\Model\Day;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Constraint\Doctrine\UniqueConstraint;

final class DayMapping implements ValidationMappingProviderInterface
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
        return Day::class;
    }

    /**
     * @param string $path
     *
     * @return ValidationClassMappingInterface|null
     */
    public function getValidationClassMapping(string $path)
    {
        return ValidationClassMappingBuilder::create([
            new UniqueConstraint($this->objectManager, ['date', 'user']),
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
            ValidationPropertyMappingBuilder::create('date', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new DateTimeConstraint(),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('weight', [
                new NumericConstraint(),
                new NumericRangeConstraint(0),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('comestiblesWithinDay', [
                new ValidConstraint(),
            ])->getMapping(),
        ];
    }
}
