<?php

namespace Energycalculator\Model\Traits;

use Energycalculator\Model\User;

trait OwnedByUserTrait
{
    /**
     * @var User|\Closure|null
     */
    private $user;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param User $user
     *
     * @return self
     */
    public function withUser(User $user)
    {
        $model = $this->cloneWithModification(__METHOD__, $user->getId(), $this->userId);
        $model->user = $user;
        $model->userId = $user->getId();

        return $model;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user instanceof \Closure) {
            $user = $this->user;
            $this->user = $user();
        }

        return $this->user;
    }

    /**
     * @return string
     */
    public function getOwnedByUserId(): string
    {
        return $this->userId;
    }
}
