<?php

declare(strict_types=1);

namespace Energycalculator\Validation;

use Chubbyphp\Model\ResolverInterface;
use Chubbyphp\Validation\Constraint\ConstraintInterface;
use Chubbyphp\Validation\Constraint\DateConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Constraint\NumericConstraint;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\ValidationModel\Constraint\ModelCollectionConstraint;
use Chubbyphp\ValidationModel\Constraint\ModelReferenceConstraint;
use Chubbyphp\ValidationModel\Constraint\UniqueModelConstraint;
use Energycalculator\Model\Day;

class DayMapping implements ObjectMappingInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return Day::class;
    }

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints(): array
    {
        return [new UniqueModelConstraint($this->resolver, ['date'])];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('user', [new ModelReferenceConstraint(false)]),
            new PropertyMapping('date', [new NotNullConstraint(), new DateConstraint()]),
            new PropertyMapping('weight', [new NumericConstraint()]),
            new PropertyMapping('comestiblesWithinDay', [new ModelCollectionConstraint()]),
        ];
    }
}
