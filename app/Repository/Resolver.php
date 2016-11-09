<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\RepositoryInterface;

class Resolver
{
    /**
     * @param RepositoryInterface $repository
     * @param array $criteria
     * @return \Closure
     */
    public function getManyResolver(RepositoryInterface $repository, array $criteria)
    {
        return function () use ($repository, $criteria) {
            return $repository->findBy($criteria);
        };
    }

    /**
     * @param RepositoryInterface $repository
     * @param array $criteria
     * @return \Closure
     */
    public function getOneResolver(RepositoryInterface $repository, array $criteria)
    {
        return function () use ($repository, $criteria) {
            return $repository->findOneBy($criteria);
        };
    }
}
