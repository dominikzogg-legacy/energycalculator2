<?php

namespace Energycalculator\Repository;

use Chubbyphp\Model\Collection\LazyModelCollection;
use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;
use Chubbyphp\Model\ModelInterface;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;
use Ramsey\Uuid\Uuid;

final class DayRepository extends AbstractDoctrineRepository
{
    /**
     * @return string
     */
    public static function getModelClass(): string
    {
        return Day::class;
    }

    /**
     * @return Day
     */
    public function create(): Day
    {
        $modelClass = $this->getModelClass();

        return new $modelClass(
            (string) Uuid::uuid4(),
            new \DateTime(),
            new \DateTime(),
            new ModelCollection()
        );
    }

    /**
     * @param array $row
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        $row['user'] = $this->resolver->lazyFind(User::class, $row['userId']);
        $row['comestiblesWithinDay'] = new LazyModelCollection($this->resolver->lazyFindBy(
            ComestibleWithinDay::class, ['dayId' => $row['id']], ['createdAt' => 'ASC']
        ));

        return parent::fromPersistence($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'days';
    }
}
