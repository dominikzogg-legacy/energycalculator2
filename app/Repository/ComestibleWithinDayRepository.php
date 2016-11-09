<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\AbstractDoctrineRepository;
use Chubbyphp\Model\Cache\ModelCacheInterface;
use Chubbyphp\Model\ModelInterface;
use Doctrine\DBAL\Connection;
use Energycalculator\Model\ComestibleWithinDay;
use Psr\Log\LoggerInterface;

final class ComestibleWithinDayRepository extends AbstractDoctrineRepository
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var ComestibleRepository
     */
    private $comestibleRepository;

    /**
     * @param Resolver $resolver
     * @param ComestibleRepository $comestibleRepository
     * @param Connection $connection
     * @param ModelCacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Resolver $resolver,
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
     * @param array $row
     * @return ModelInterface
     */
    protected function fromRow(array $row): ModelInterface
    {
        $row['comestible'] = $this->resolver->getOneResolver(
            $this->comestibleRepository,
            ['id' => $row['comestibleId']]
        );

        return parent::fromRow($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'comestibles';
    }
}
