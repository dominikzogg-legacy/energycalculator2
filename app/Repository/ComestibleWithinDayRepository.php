<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Cache\ModelCacheInterface;
use Chubbyphp\Model\Doctrine\DBAL\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\ResolverInterface;
use Doctrine\DBAL\Connection;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class ComestibleWithinDayRepository extends AbstractDoctrineRepository
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ComestibleRepository
     */
    private $comestibleRepository;

    /**
     * @param ResolverInterface $resolver
     * @param ComestibleRepository $comestibleRepository
     * @param Connection $connection
     * @param ModelCacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ResolverInterface $resolver,
        ComestibleRepository $comestibleRepository,
        Connection $connection,
        ModelCacheInterface $cache = null,
        LoggerInterface $logger = null
    ) {
        $this->resolver = $resolver;
        $this->comestibleRepository = $comestibleRepository;

        parent::__construct($connection, $cache, $logger);
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return ComestibleWithinDay::class;
    }

    /**
     * @param string $dayId
     * @return ComestibleWithinDay
     */
    public function create(string $dayId): ComestibleWithinDay
    {
        $modelClass = $this->getModelClass();

        return new $modelClass((string) Uuid::uuid4(), new \DateTime(), $dayId);
    }

    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function fromRow(array $row): ModelInterface
    {
        $row['comestible'] = $this->resolver->find($this->comestibleRepository, $row['comestibleId']);

        return parent::fromRow($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'comestibles_within_days';
    }
}
