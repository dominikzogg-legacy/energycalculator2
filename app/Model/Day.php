<?php

declare(strict_types=1);

namespace Energycalculator\Model;

use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Model\Collection\ModelCollectionInterface;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReference;
use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Energycalculator\Model\Traits\IdTrait;
use Energycalculator\Model\Traits\OwnedByUserTrait;
use Ramsey\Uuid\Uuid;

final class Day implements ModelInterface, OwnedByUserModelInterface, \JsonSerializable
{
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
     * @param string|null $id
     *
     * @return Day
     */
    public static function create(string $id = null): Day
    {
        $day = new self();
        $day->id = $id ?? (string) Uuid::uuid4();
        $day->user = new ModelReference();
        $day->date = (new \DateTime())->format('Y-m-d');
        $day->comestiblesWithinDay = new ModelCollection(
            ComestibleWithinDay::class, 'dayId', $day->id, []
        );

        return $day;
    }

    private function __construct()
    {
    }

    /**
     * @param \DateTime $date
     *
     * @return Day
     */
    public function setDate(\DateTime $date): Day
    {
        $this->date = $date->format('Y-m-d');

        return $this;
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
    public function setWeight(float $weight = null): Day
    {
        $this->weight = $weight;

        return $this;
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
}
