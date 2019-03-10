<?php

declare(strict_types=1);

namespace Energycalculator\Model;

use Chubbyphp\Security\Authorization\OwnedByUserModelInterface as BaseOwnedByUserModelInterface;

interface OwnedByUserModelInterface extends ModelInterface, BaseOwnedByUserModelInterface
{
    /**
     * @param User $user
     */
    public function setUser(User $user): void;

    /**
     * @return User|null
     */
    public function getUser();
}
