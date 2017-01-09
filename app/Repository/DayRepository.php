<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Collection\LazyModelCollection;
use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\LazyModelReference;
use Chubbyphp\Security\UserInterface;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;
use Ramsey\Uuid\Uuid;

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
     * @param UserInterface $user
     * @return Day
     */
    public function create(UserInterface $user): Day
    {
        return Day::create((string) Uuid::uuid4(), new \DateTime(), new \DateTime(), $user);
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
