<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Cache\ModelCacheInterface;
use Chubbyphp\Model\Collection\LazyModelCollection;
use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Model\Doctrine\DBAL\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\ResolverInterface;
use Doctrine\DBAL\Connection;
use Energycalculator\Model\Day;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class DayRepository extends AbstractDoctrineRepository
{
    /**
     * @var ResolverInterface
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
     * @param ResolverInterface $resolver
     * @param ComestibleWithinDayRepository $comestibleWithinDayRepository
     * @param UserRepository $userRepository
     * @param Connection $connection
     * @param ModelCacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ResolverInterface $resolver,
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
     * @return Day
     */
    public function create(): Day
    {
        return new Day(
            (string) Uuid::uuid4(),
            new \DateTime(),
            new \DateTime(),
            new ModelCollection($this->comestibleWithinDayRepository)
        );
    }

    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function fromRow(array $row): ModelInterface
    {
        $row['user'] = $this->resolver->find($this->userRepository, $row['userId']);
        $row['comestiblesWithinDay'] = new LazyModelCollection(
            $this->comestibleWithinDayRepository,
            ['dayId' => $row['id']],
            ['createdAt' => 'ASC'])
        ;

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
