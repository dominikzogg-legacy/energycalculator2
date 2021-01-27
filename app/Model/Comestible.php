<?php

namespace Energycalculator\Model;

use Ramsey\Uuid\Uuid;

final class Comestible implements OwnedByUserModelInterface
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
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $calorie = 0;

    /**
     * @var float
     */
    private $protein = 0;

    /**
     * @var float
     */
    private $carbohydrate = 0;

    /**
     * @var float
     */
    private $fat = 0;

    /**
     * @var float|null
     */
    private $defaultValue;

    public function __construct()
    {
        $this->id = (string) Uuid::uuid4();
        $this->createdAt = new \DateTime();
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
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param float $calorie
     */
    public function setCalorie(float $calorie): void
    {
        $this->calorie = $calorie;
    }

    /**
     * @return float
     */
    public function getCalorie(): float
    {
        return $this->calorie;
    }

    /**
     * @param float $protein
     */
    public function setProtein(float $protein): void
    {
        $this->protein = $protein;
    }

    /**
     * @return float
     */
    public function getProtein(): float
    {
        return $this->protein;
    }

    /**
     * @param float $carbohydrate
     */
    public function setCarbohydrate(float $carbohydrate): void
    {
        $this->carbohydrate = $carbohydrate;
    }

    /**
     * @return float
     */
    public function getCarbohydrate(): float
    {
        return $this->carbohydrate;
    }

    /**
     * @param float $fat
     */
    public function setFat(float $fat): void
    {
        $this->fat = $fat;
    }

    /**
     * @return float
     */
    public function getFat(): float
    {
        return $this->fat;
    }

    /**
     * @param float|null $defaultValue
     */
    public function setDefaultValue(?float $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return float|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->jsonSerialize(),
            'name' => $this->name,
            'calorie' => $this->calorie,
            'protein' => $this->protein,
            'carbohydrate' => $this->carbohydrate,
            'fat' => $this->fat,
            'defaultValue' => $this->defaultValue,
        ];
    }
}
