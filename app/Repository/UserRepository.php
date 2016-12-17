<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Energycalculator\Model\User;
use Ramsey\Uuid\Uuid;

final class UserRepository extends AbstractDoctrineRepository
{
    /**
     * @param string $modelClass
     *
     * @return bool
     */
    public function isResponsible(string $modelClass): bool
    {
        return $modelClass === User::class;
    }

    /**
     * @return User
     */
    public function create(): User
    {
        return User::create((string) Uuid::uuid4(), new \DateTime());
    }

    /**
     * @param array $row
     *
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        return User::fromPersistence($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'users';
    }
}
