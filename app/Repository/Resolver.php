<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\RepositoryInterface;

class Resolver
{
    /**
     * @param RepositoryInterface $repository
     * @param string|null $id
     * @return \Closure|null
     */
    public function getOneResolver(RepositoryInterface $repository, string $id = null)
    {
        if (null === $id) {
            return null;
        }

        return function () use ($repository, $id) {
            return $repository->find($id);
        };
    }
}
