<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;
use Ramsey\Uuid\Uuid;

final class ComestibleWithinDayRepository extends AbstractDoctrineRepository
{
    /**
     * @param string $modelClass
     *
     * @return bool
     */
    public function isResponsible(string $modelClass): bool
    {
        return $modelClass === ComestibleWithinDay::class;
    }

    /**
     * @param string $dayId
     *
     * @return ComestibleWithinDay
     */
    public function create(string $dayId): ComestibleWithinDay
    {
        return new ComestibleWithinDay((string) Uuid::uuid4(), new \DateTime(), $dayId);
    }

    /**
     * @param array $row
     *
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        $row['comestible'] = $this->resolver->lazyFind(Comestible::class, $row['comestibleId']);

        return ComestibleWithinDay::fromPersistence($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'comestibles_within_days';
    }
}
