<?php

declare(strict_types=1);

namespace Energycalculator\Repository;

use Chubbyphp\Security\UserRepositoryInterface;
use Energycalculator\Collection\CollectionInterface;
use Energycalculator\Model\ModelInterface;

class UserRepository implements RepositoryInterface, UserRepositoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CollectionInterface $collection
     */
    public function resolveCollection(CollectionInterface $collection): void
    {
        $this->repository->resolveCollection($collection);
    }

    /**
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function find(string $id): ?ModelInterface
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return ModelInterface|null
     */
    public function findOneBy(array $criteria): ?ModelInterface
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param ModelInterface $model
     */
    public function persist(ModelInterface $model): void
    {
        $this->repository->persist($model);
    }

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model): void
    {
        $this->repository->remove($model);
    }

    public function flush(): void
    {
        $this->repository->flush();
    }

    /**
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function findByUsername(string $username)
    {
        return $this->repository->findOneBy(['email' => $username]);
    }
}
