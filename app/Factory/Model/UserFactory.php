<?php

declare(strict_types=1);

namespace Energycalculator\Factory\Model;

use Energycalculator\Factory\ModelFactoryInterface;
use Energycalculator\Model\ModelInterface;
use Energycalculator\Model\User;

final class UserFactory implements ModelFactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface
    {
        return new User();
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return User::class;
    }
}
