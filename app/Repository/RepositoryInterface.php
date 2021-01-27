<?php

declare(strict_types=1);

namespace Energycalculator\Repository;

use Energycalculator\Collection\CollectionInterface;
use Energycalculator\Model\ModelInterface;

interface RepositoryInterface
{
    /**
     * @param CollectionInterface $collection
     */
    public function resolveCollection(CollectionInterface $collection): void;

    /**
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function find(string $id): ?ModelInterface;

    /**
     * @param array $criteria
     *
     * @return ModelInterface|null
     */
    public function findOneBy(array $criteria): ?ModelInterface;

    /**
     * @param ModelInterface $model
     */
    public function persist(ModelInterface $model): void;

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model): void;

    public function flush(): void;
}
