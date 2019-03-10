<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Deserialization;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Security\Authorization\RoleHierarchyResolverInterface;
use Energycalculator\Model\User;

final class UserMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var RoleHierarchyResolverInterface
     */
    private $roleHierarchyResolver;

    /**
     * @param PasswordManagerInterface       $passwordManager
     * @param RoleHierarchyResolverInterface $roleHierarchyResolver
     */
    public function __construct(
        PasswordManagerInterface $passwordManager,
        RoleHierarchyResolverInterface $roleHierarchyResolver
    ) {
        $this->passwordManager = $passwordManager;
        $this->roleHierarchyResolver = $roleHierarchyResolver;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return User::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(
        string $path,
        string $type = null
    ): callable {
        return function () {
            return new User();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(
        string $path,
        string $type = null
    ): array {
        return [
            DenormalizationFieldMappingBuilder::create('email', true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createCallback(
                'password',
                function (
                    string $path,
                    $object,
                    $value
                ) {
                    $value = $this->passwordManager->hash($value);

                    $accessor = new PropertyAccessor('password');
                    $accessor->setValue($object, $value);
                }
            )->getMapping(),
            DenormalizationFieldMappingBuilder::createCallback(
                'roles',
                function (
                    string $path,
                    $object,
                    $value
                ) {
                    $possibleRoles = $this->roleHierarchyResolver->resolve(['ADMIN']);

                    foreach ($value as $i => $role) {
                        if (!in_array($role, $possibleRoles, true)) {
                            unset($value[$i]);
                        }
                    }

                    $accessor = new PropertyAccessor('roles');
                    $accessor->setValue($object, $value);
                }
            )->getMapping(),
        ];
    }
}
