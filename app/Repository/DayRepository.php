<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Collection\LazyModelCollection;
use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\LazyModelReference;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;

final class DayRepository extends AbstractDoctrineRepository
{
    /**
     * @param string $modelClass
     *
     * @return bool
     */
    public function isResponsible(string $modelClass): bool
    {
        return $modelClass === Day::class;
    }

    /**
     * @param array $row
     *
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        $row['user'] = new LazyModelReference($this->resolver, User::class, $row['userId']);

        $row['comestiblesWithinDay'] = new LazyModelCollection(
            $this->resolver, ComestibleWithinDay::class, 'dayId', $row['id'], ['createdAt' => 'ASC']
        );

        return Day::fromPersistence($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'days';
    }
}
