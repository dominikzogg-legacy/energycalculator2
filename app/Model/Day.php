<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Model\Collection\ModelCollectionInterface;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReference;
use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Chubbyphp\Security\UserInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Energycalculator\Model\Traits\CloneWithModificationTrait;
use Energycalculator\Model\Traits\CreatedAndUpdatedAtTrait;
use Energycalculator\Model\Traits\IdTrait;
use Energycalculator\Model\Traits\OwnedByUserTrait;
use Respect\Validation\Rules\FloatVal;
use Respect\Validation\Validator as v;

final class Day implements OwnedByUserModelInterface, ValidatableModelInterface
{
    use CloneWithModificationTrait;
    use CreatedAndUpdatedAtTrait;
    use IdTrait;
    use OwnedByUserTrait;

    /**
     * @var string
     */
    private $date;

    /**
     * @var float|null
     */
    private $weight;

    /**
     * @var ModelCollectionInterface
     */
    private $comestiblesWithinDay;

    /**
     * @param string $id
     * @param \DateTime $createdAt
     * @param \DateTime $date
     * @param UserInterface $user
     * @return Day
     */
    public static function create(string $id, \DateTime $createdAt, \DateTime $date, UserInterface $user): Day
    {
        $day = new self();
        $day->id = $id;
        $day->setCreatedAt($createdAt);
        $day->date = $date->format('Y-m-d');
        $day->user = (new ModelReference())->setModel($user);
        $day->comestiblesWithinDay = new ModelCollection(
            ComestibleWithinDay::class, 'dayId', $id, ['createdAt' => 'ASC']
        );

        return $day;
    }

    private function __construct() {}

    /**
     * @param \DateTime $date
     *
     * @return Day
     */
    public function withDate(\DateTime $date): Day
    {
        $date = $date->format('Y-m-d');
        $day = $this->cloneWithModification(__METHOD__, $date, $this->date);
        $day->date = $date;

        return $day;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return new \DateTime($this->date);
    }

    /**
     * @param float|null $weight
     *
     * @return Day
     */
    public function withWeight(float $weight = null): Day
    {
        $day = $this->cloneWithModification(__METHOD__, $weight, $this->weight);
        $day->weight = $weight;

        return $day;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param array $comestiblesWithinDay
     *
     * @return Day
     */
    public function setComestiblesWithinDay(array $comestiblesWithinDay): Day
    {
        $this->comestiblesWithinDay->setModels($comestiblesWithinDay);

        return $this;
    }

    /**
     * @return ComestibleWithinDay[]|array
     */
    public function getComestiblesWithinDay(): array
    {
        return array_values($this->comestiblesWithinDay->getModels());
    }

    /**
     * @return float
     */
    public function getCalorie()
    {
        $calorie = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $calorie += $comestiblesWithinDay->getCalorie();
        }

        return $calorie;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        $protein = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $protein += $comestiblesWithinDay->getProtein();
        }

        return $protein;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        $carbohydrate = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $carbohydrate += $comestiblesWithinDay->getCarbohydrate();
        }

        return $carbohydrate;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        $fat = 0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $fat += $comestiblesWithinDay->getFat();
        }

        return $fat;
    }

    /**
     * @param array $data
     *
     * @return Day|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $day = new self();

        $day->id = $data['id'];
        $day->createdAt = $data['createdAt'];
        $day->updatedAt = $data['updatedAt'];
        $day->date = $data['date'];
        $day->weight = $data['weight'];
        $day->comestiblesWithinDay = $data['comestiblesWithinDay'];
        $day->user = $data['user'];

        return $day;
    }

    /**
     * @return array
     */
    public function toPersistence(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'userId' => $this->user->getId(),
            'date' => $this->date,
            'weight' => $this->weight,
            'comestiblesWithinDay' => $this->comestiblesWithinDay,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'user' => $this->user->jsonSerialize(),
            'date' => $this->date,
            'weight' => $this->weight,
            'calorie' => $this->getCalorie(),
            'protein' => $this->getProtein(),
            'carbohydrate' => $this->getCarbohydrate(),
            'fat' => $this->getFat(),
            'comestiblesWithinDay' => $this->comestiblesWithinDay->jsonSerialize(),
        ];
    }

    /**
     * @return v|null
     */
    public function getModelValidator()
    {
        return v::create()->addRule(new UniqueModelRule(['user', 'date']));
    }

    /**
     * @return v[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'date' => v::date(),
            'weight' => v::optional(new FloatVal()),
        ];
    }
}
