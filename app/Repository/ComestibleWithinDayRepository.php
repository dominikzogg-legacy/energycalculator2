<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\LazyModelReference;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
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
     * @return ComestibleWithinDay
     */
    public function create(): ComestibleWithinDay
    {
        return ComestibleWithinDay::create((string) Uuid::uuid4(), new \DateTime());
    }

    /**
     * @param array $row
     *
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        $row['comestible'] = new LazyModelReference($this->resolver, Comestible::class, $row['comestibleId']);

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
