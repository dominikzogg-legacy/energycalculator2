<?php

namespace Energycalculator\Deserializer;

use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Security\Authorization\RoleHierarchyResolverInterface;
use Energycalculator\Model\User;

class UserMapping implements ObjectMappingInterface
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
     * @param PasswordManagerInterface $passwordManager
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
     * @return string
     */
    public function getConstructMethod(): string
    {
        return 'create';
    }

    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping(
                'email',
                function (DeserializerInterface $deserializer, $newEmail, $oldEmail, $object) {
                    $reflectionProperty = new \ReflectionProperty(get_class($object), 'username');
                    $reflectionProperty->setAccessible(true);
                    $reflectionProperty->setValue($object, $newEmail);

                    return $newEmail;
                }
            ),
            new PropertyMapping(
                'password',
                function (DeserializerInterface $deserializer, $newPlainPassword, $oldPassword) {
                    if (!$newPlainPassword) {
                        return $oldPassword;
                    }

                    return $this->passwordManager->hash($newPlainPassword);
                }
            ),
            new PropertyMapping(
                'roles',
                function (DeserializerInterface $deserializer, $serializedRoles) {
                    $possibleRoles = $this->roleHierarchyResolver->resolve(['ADMIN']);

                    foreach ($serializedRoles as $i => $role) {
                        if (!in_array($role, $possibleRoles, true)) {
                            unset($serializedRoles[$i]);
                        }
                    }

                    return $serializedRoles;
                }
            ),
        ];
    }
}
