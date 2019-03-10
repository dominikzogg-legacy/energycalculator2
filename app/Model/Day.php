<?php

namespace Energycalculator\Model;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;

final class Day implements OwnedByUserModelInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float|null
     */
    private $weight;

    /**
     * @var Collection
     */
    private $comestiblesWithinDay;

    public function __construct()
    {
        $this->id = (string) Uuid::uuid4();
        $this->createdAt = new \DateTime();
        $this->date = new \DateTime();
        $this->comestiblesWithinDay = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getOwnedByUserId(): string
    {
        return $this->user->getId();
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param float|null $weight
     */
    public function setWeight(?float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return float|null
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param array $comestiblesWithinDay
     */
    public function setComestiblesWithinDay(array $comestiblesWithinDay): void
    {
        $this->comestiblesWithinDay = new ArrayCollection($comestiblesWithinDay);
    }

    /**
     * @return ComestibleWithinDay[]|array
     */
    public function getComestiblesWithinDay(): array
    {
        return $this->comestiblesWithinDay->toArray();
    }

    /**
     * @return float
     */
    public function getCalorie(): float
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
    public function getProtein(): float
    {
        $protein = 0.0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $protein += $comestiblesWithinDay->getProtein();
        }

        return $protein;
    }

    /**
     * @return float
     */
    public function getCarbohydrate(): float
    {
        $carbohydrate = 0.0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $carbohydrate += $comestiblesWithinDay->getCarbohydrate();
        }

        return $carbohydrate;
    }

    /**
     * @return float
     */
    public function getFat(): float
    {
        $fat = 0.0;
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $fat += $comestiblesWithinDay->getFat();
        }

        return $fat;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $comestiblesWithinDay = [];
        foreach ($this->getComestiblesWithinDay() as $comestiblesWithinDay) {
            $comestiblesWithinDay[] = $comestiblesWithinDay->jsonSerialize();
        }

        return [
            'id' => $this->id,
            'user' => $this->user->jsonSerialize(),
            'date' => $this->date->format('Y-m-d'),
            'weight' => $this->weight,
            'calorie' => $this->getCalorie(),
            'protein' => $this->getProtein(),
            'carbohydrate' => $this->getCarbohydrate(),
            'fat' => $this->getFat(),
            'comestiblesWithinDay' => $comestiblesWithinDay,
        ];
    }
}
