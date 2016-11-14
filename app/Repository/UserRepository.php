<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Doctrine\DBAL\AbstractDoctrineRepository;
use Energycalculator\Model\User;
use Ramsey\Uuid\Uuid;

final class UserRepository extends AbstractDoctrineRepository
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return User::class;
    }

    /**
     * @return User
     */
    public function create(): User
    {
        return new User((string) Uuid::uuid4(), new \DateTime());
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'users';
    }
}
