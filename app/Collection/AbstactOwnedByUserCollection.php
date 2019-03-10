<?php

declare(strict_types=1);

namespace Energycalculator\Collection;

use Energycalculator\Model\User;

abstract class AbstactOwnedByUserCollection extends AbstractCollection
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        $data['user'] = $this->getUser()->jsonSerialize();

        return $data;
    }
}
