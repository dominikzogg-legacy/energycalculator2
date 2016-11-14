<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Cache\ModelCacheInterface;
use Chubbyphp\Model\Doctrine\DBAL\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\ResolverInterface;
use Doctrine\DBAL\Connection;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\User;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class ComestibleRepository extends AbstractDoctrineRepository
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param ResolverInterface $resolver
     * @param UserRepository $userRepository
     * @param Connection $connection
     * @param ModelCacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ResolverInterface $resolver,
        UserRepository $userRepository,
        Connection $connection,
        ModelCacheInterface $cache = null,
        LoggerInterface $logger = null
    ) {
        $this->resolver = $resolver;
        $this->userRepository = $userRepository;

        parent::__construct($connection, $cache, $logger);
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Comestible::class;
    }

    /**
     * @return Comestible
     */
    public function create(): Comestible
    {
        $modelClass = $this->getModelClass();

        return new $modelClass((string) Uuid::uuid4(), new \DateTime());
    }

    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function fromRow(array $row): ModelInterface
    {
        $row['user'] = $this->resolver->find($this->userRepository, $row['userId']);

        return parent::fromRow($row);
    }

    /**
     * @param string $userId
     * @param string $name
     * @return array
     */
    public function findRowsByNameLike(string $userId, string $name): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('id,name,defaultValue')->from($this->getTable());

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
