<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\AbstractDoctrineRepository;
use Chubbyphp\Model\Cache\ModelCacheInterface;
use Chubbyphp\Model\ModelInterface;
use Doctrine\DBAL\Connection;
use Energycalculator\Model\Comestible;
use Psr\Log\LoggerInterface;

final class ComestibleRepository extends AbstractDoctrineRepository
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     * @param Connection $connection
     * @param ModelCacheInterface|null $cache
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        UserRepository $userRepository,
        Connection $connection,
        ModelCacheInterface $cache = null,
        LoggerInterface $logger = null
    ) {
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
     * @param array $row
     * @return ModelInterface
     */
    protected function fromRow(array $row): ModelInterface
    {
        $row['user'] = function () use ($row) {
            if (null === $row['userId']) {
                return null;
            }

            return $this->userRepository->find($row['userId']);
        };

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
