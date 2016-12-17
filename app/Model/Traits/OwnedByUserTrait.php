<?php

namespace Energycalculator\Model\Traits;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReferenceInterface;
use Energycalculator\Model\User;

trait OwnedByUserTrait
{
    /**
     * @var ModelReferenceInterface
     */
    private $user;

    /**
     * @return User|ModelInterface|null
     */
    public function getUser()
    {
        return $this->user->getModel();
    }

    /**
     * @return string
     */
    public function getOwnedByUserId(): string
    {
        return $this->user->getId();
    }
}
