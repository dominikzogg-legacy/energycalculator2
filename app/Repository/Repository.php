<?php

declare(strict_types=1);

namespace Energycalculator\Repository;

use Energycalculator\Collection\CollectionInterface;
use Energycalculator\Model\ModelInterface;
use Doctrine\ORM\EntityManager;
use Energycalculator\Collection\AbstactOwnedByUserCollection;

final class Repository implements RepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @param EntityManager $entityManager
     * @param string        $modelClass
     */
    public function __construct(EntityManager $entityManager, string $modelClass)
    {
        $this->entityManager = $entityManager;
        $this->modelClass = $modelClass;
    }

    /**
     * @param CollectionInterface $collection
     */
    public function resolveCollection(CollectionInterface $collection): void
    {
        $qb = $this->entityManager->getRepository($this->modelClass)->createQueryBuilder('m');

        if ($collection instanceof AbstactOwnedByUserCollection) {
            $qb->andWhere($qb->expr()->eq('m.user', ':user'));
            $qb->setParameter('user', $collection->getUser());
        }

        $countQb = clone $qb;
        $countQb->select($qb->expr()->count('m.id'));

        $collection->setCount((int) $countQb->getQuery()->getSingleScalarResult());

        $elementsQb = clone $qb;

        foreach ($collection->getSort() as $field => $order) {
            $elementsQb->addOrderBy(sprintf('m.%s', $field), $order);
        }

        $limit = $collection->getPerPage();
        $offset = $collection->getPage() * $limit - $limit;

        $elementsQb->setFirstResult($offset);
        $elementsQb->setMaxResults($limit);

        $collection->setelements($elementsQb->getQuery()->getResult());
    }

    /**
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function find(string $id): ?ModelInterface
    {
        return $this->entityManager->find($this->modelClass, $id);
    }

    /**
     * @param array $criteria
     *
     * @return ModelInterface|null
     */
    public function findOneBy(array $criteria): ?ModelInterface
    {
        return $this->entityManager->getRepository($this->modelClass)->findOneBy($criteria);
    }

    /**
     * @param ModelInterface $model
     */
    public function persist(ModelInterface $model): void
    {
        $this->entityManager->persist($model);
    }

    /**
     * @param ModelInterface $model
     */
    public function remove(ModelInterface $model): void
    {
        $this->entityManager->remove($model);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
