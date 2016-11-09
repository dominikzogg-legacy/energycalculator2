<?php

namespace Energycalculator\Repository\Traits;

use Energycalculator\Repository\UserRepository;

trait UserResolverTrait
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param string|null $id
     * @return \Closure|null
     */
    private function getUserResolver(string $id = null)
    {
        if (null === $id) {
            return null;
        }

        return function () use ($id) {
            return $this->userRepository->find($id);
        };
    }
}
