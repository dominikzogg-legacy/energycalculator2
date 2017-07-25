<?php

declare(strict_types=1);

namespace Energycalculator\Repository;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\LazyModelReference;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\User;
use Energycalculator\Search\ComestibleSearch;

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
     * @param ComestibleSearch $search
     *
     * @return ComestibleSearch
     */
    public function search(ComestibleSearch $search): ComestibleSearch
    {
        $criteria = ['userId' => $search->getUserId()];
        $orderBy = [$search->getSort() => $search->getOrder()];
        $limit = $search->getPerPage();
        $offset = $search->getPage() * $search->getPerPage() - $search->getPerPage();

        $search->setElements($this->findBy($criteria, $orderBy, $limit, $offset));
        $search->setElementCount($this->countBy($criteria));

        return $search;
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
