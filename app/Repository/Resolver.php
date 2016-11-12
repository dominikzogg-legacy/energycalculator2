<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\RepositoryInterface;
use Energycalculator\Repository\Collection\LazyPersistedModelCollection;

class Resolver
{
    /**
     * @param RepositoryInterface $repository
     * @param string $id
     * @return \Closure
     */
    public function getFindResolver(RepositoryInterface $repository, string $id)
    {
        return function () use ($repository, $id) {
            return $repository->find($id);
        };
    }

    /**
     * @param RepositoryInterface $repository
     * @param array $criteria
     * @return \Closure
     */
    public function getFindOneByResolver(RepositoryInterface $repository, array $criteria)
    {
        return function () use ($repository, $criteria) {
            return $repository->findOneBy($criteria);
        };
    }

    /**
     * @param RepositoryInterface $repository
     * @param array $criteria
     * @return LazyPersistedModelCollection
     */
    public function getLazyPersistedModelCollection(RepositoryInterface $repository, array $criteria)
    {
        return new LazyPersistedModelCollection(function () use ($repository, $criteria) {
            return $repository->findBy($criteria);
        });
    }
}
