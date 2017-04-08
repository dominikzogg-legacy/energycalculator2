<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\LazyModelReference;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\User;

final class ComestibleRepository extends AbstractRepository
{
    /**
     * @param string $modelClass
     *
     * @return bool
     */
    public function isResponsible(string $modelClass): bool
    {
        return $modelClass === Comestible::class;
    }

    /**
     * @param array $row
     *
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        $row['user'] = new LazyModelReference($this->resolver, User::class, $row['userId']);

        return Comestible::fromPersistence($row);
    }

    /**
     * @param string $userId
     * @param string $name
     *
     * @return array
     */
    public function findRowsByNameLike(string $userId, string $name): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('id,name AS text,defaultValue AS amount')->from($this->getTable());

        $qb->andWhere($qb->expr()->eq('userId', ':userId'));
        $qb->setParameter('userId', $userId);

        $qb->andWhere($qb->expr()->like('name', ':name'));
        $qb->setParameter('name', '%'.$name.'%');

        $qb->addOrderBy('name', 'ASC');

        return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'comestibles';
    }
}
