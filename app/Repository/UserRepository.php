<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\AbstractDoctrineRepository;
use Energycalculator\Model\User;

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
     * @return string
     */
    protected function getTable(): string
    {
        return 'users';
    }
}
