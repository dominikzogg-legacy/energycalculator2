<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Cache\ModelCacheInterface;
use Chubbyphp\Model\Doctrine\DBAL\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\ResolverInterface;
use Doctrine\DBAL\Connection;
use Energycalculator\Model\ComestibleWithinDay;
use Psr\Log\LoggerInterface;

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
     * @var DayRepository
     */
    private $dayRepository;

    /**
     * @param ResolverInterface $resolver
     * @param ComestibleRepository $comestibleRepository
     * @param DayRepository $dayRepository
     * @param Connection $connection
     * @param ModelCacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ResolverInterface $resolver,
        ComestibleRepository $comestibleRepository,
        DayRepository $dayRepository,
        Connection $connection,
        ModelCacheInterface $cache = null,
        LoggerInterface $logger = null
    ) {
        $this->resolver = $resolver;
        $this->comestibleRepository = $comestibleRepository;
        $this->dayRepository = $dayRepository;

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
        $row['comestible'] = $this->resolver->find($this->comestibleRepository, $row['comestibleId']);
        $row['day'] = $this->resolver->find($this->dayRepository, $row['dayId']);

        return parent::fromRow($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'comestible_within_days';
    }
}
