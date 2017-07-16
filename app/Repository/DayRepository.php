<?php

declare(strict_types=1);

namespace Energycalculator\Repository;

use Chubbyphp\Model\Collection\LazyModelCollection;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\LazyModelReference;
use Doctrine\DBAL\Query\QueryBuilder;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;

final class DayRepository extends AbstractRepository
{
    /**
     * @param string $modelClass
     *
     * @return bool
     */
    public function isResponsible(string $modelClass): bool
    {
        return $modelClass === Day::class;
    }

    /**
     * @param array $row
     *
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        $row['user'] = new LazyModelReference($this->resolver, User::class, $row['userId']);

        $row['comestiblesWithinDay'] = new LazyModelCollection(
            $this->resolver, ComestibleWithinDay::class, 'dayId', $row['id'], ['sorting' => 'ASC']
        );

        return Day::fromPersistence($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'days';
    }

    /**
     * @param  \DateTime $from
     * @param  \DateTime $to
     * @param  User      $user
     * @return Day[]
     */
    public function getInRange(\DateTime $from, \DateTime $to, User $user = null)
    {
        $qb = $this->getInRangeQueryBuilder($from, $to, $user);

        $rows = $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);

        if ([] === $rows) {
            return [];
        }

        $models = [];
        foreach ($rows as $row) {
            $models[] = $this->fromPersistence($row);
        }

        return $models;
    }

    /**
     * @param  \DateTime $from
     * @param  \DateTime $to
     * @param  User      $user
     * @return QueryBuilder
     */
    public function getInRangeQueryBuilder(\DateTime $from, \DateTime $to, User $user = null)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*');
        $qb->from('days');
        $qb->andWhere('date >= :from');
        $qb->andWhere('date <= :to');
        $qb->setParameter('from', $from->format('Y-m-d'));
        $qb->setParameter('to', $to->format('Y-m-d'));
        $qb->orderBy('date', 'ASC');

        if (null !== $user) {
            $qb->andWhere('userId = :userId');
            $qb->setParameter('userId', $user->getId());
        }

        return $qb;
    }
}
