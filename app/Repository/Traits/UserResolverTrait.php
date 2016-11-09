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
     * @return \Closure
     */
    private function getUserResolver(string $id = null): \Closure
    {
        return function () use ($id) {
            if (null === $id) {
                return null;
            }

            return $this->userRepository->find($id);
        };
    }
}
