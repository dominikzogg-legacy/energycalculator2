<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\AbstractDoctrineRepository;
use Chubbyphp\Model\Cache\ModelCacheInterface;
use Chubbyphp\Model\ModelInterface;
use Doctrine\DBAL\Connection;
use Energycalculator\Model\Day;
use Psr\Log\LoggerInterface;

final class DayRepository extends AbstractDoctrineRepository
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var ComestibleWithinDayRepository
     */
    private $comestibleWithinDayRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param Resolver $resolver
     * @param ComestibleWithinDayRepository $comestibleWithinDayRepository
     * @param UserRepository $userRepository
     * @param Connection $connection
     * @param ModelCacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Resolver $resolver,
        ComestibleWithinDayRepository $comestibleWithinDayRepository,
        UserRepository $userRepository,
        Connection $connection,
        ModelCacheInterface $cache = null,
        LoggerInterface $logger = null
    ) {
        $this->resolver = $resolver;
        $this->comestibleWithinDayRepository = $comestibleWithinDayRepository;
        $this->userRepository = $userRepository;

        parent::__construct($connection, $cache, $logger);
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return Day::class;
    }

    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function fromRow(array $row): ModelInterface
    {
        $row['user'] = $this->resolver->getFindResolver($this->userRepository, $row['userId']);
        $row['comestibleWithinDays'] = $this->resolver->getLazyPersistedModelCollection(
            $this->comestibleWithinDayRepository, ['dayId' => $row['id']]
        );

        return parent::fromRow($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'days';
    }
}
