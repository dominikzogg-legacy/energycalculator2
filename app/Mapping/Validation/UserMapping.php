<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Validation;

use Chubbyphp\Validation\Constraint\Doctrine\UniqueConstraint;
use Chubbyphp\Validation\Constraint\EmailConstraint;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Mapping\ValidationClassMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingBuilder;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Energycalculator\Model\User;

final class UserMapping implements ValidationMappingProviderInterface
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
        return User::class;
    }

    /**
     * @param string $path
     *
     * @return ValidationClassMappingInterface|null
     */
    public function getValidationClassMapping(string $path)
    {
        return ValidationClassMappingBuilder::create([
            new UniqueConstraint($this->objectManager, ['email']),
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
            ValidationPropertyMappingBuilder::create('email', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
                new EmailConstraint(),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('password', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
            ])->getMapping(),
            ValidationPropertyMappingBuilder::create('roles', [
                new NotNullConstraint(),
                new NotBlankConstraint(),
            ])->getMapping(),
        ];
    }
}
