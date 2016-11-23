<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\User;
use Ramsey\Uuid\Uuid;

final class ComestibleRepository extends AbstractDoctrineRepository
{
    /**
     * @return string
     */
    public static function getModelClass(): string
    {
        return Comestible::class;
    }

    /**
     * @return Comestible
     */
    public function create(): Comestible
    {
        $modelClass = self::getModelClass();

        return new $modelClass((string) Uuid::uuid4(), new \DateTime());
    }

    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        $row['user'] = $this->resolver->lazyFind(User::class, $row['userId']);

        return parent::fromPersistence($row);
    }

    /**
     * @param string $userId
     * @param string $name
     * @return array
     */
    public function findRowsByNameLike(string $userId, string $name): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('id,name AS text,defaultValue AS amount')->from($this->getTable());

        $qb->andWhere($qb->expr()->eq('userId', ':userId'));
        $qb->setParameter('userId', $userId);

        $qb->andWhere($qb->expr()->like('name', ':name'));
        $qb->setParameter('name', '%' . $name . '%');

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
