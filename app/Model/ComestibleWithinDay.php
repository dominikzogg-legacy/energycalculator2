<?php

namespace Energycalculator\Model;

use Ramsey\Uuid\Uuid;

final class ComestibleWithinDay implements ModelInterface
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
     * @var Day
     */
    private $day;

    /**
     * @var Day
     */
    private $comestible;

    /**
     * @var int
     */
    private $sorting;

    /**
     * @var float
     */
    private $amount = 0;

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
     * @param DayInterface $day
     */
    public function setDay(DayInterface $day): void
    {
        $this->day = $day;
    }

    /**
     * @return DayInterface|null
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param ComestibleInterface $comestible
     */
    public function setComestible(ComestibleInterface $comestible): void
    {
        $this->comestible = $comestible;
    }

    /**
     * @return ComestibleInterface|null
     */
    public function getComestible()
    {
        return $this->comestible;
    }

    /**
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getComestible()->getName();
    }

    /**
     * @return float
     */
    public function getCalorie()
    {
        if (null === $this->getComestible() || null === $this->amount) {
            return 0;
        }

        return $this->getComestible()->getCalorie() * (float) $this->amount / 100;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        if (null === $this->getComestible() || null === $this->amount) {
            return 0;
        }

        return $this->getComestible()->getProtein() * (float) $this->amount / 100;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        if (null === $this->getComestible() || null === $this->amount) {
            return 0;
        }

        return $this->getComestible()->getCarbohydrate() * (float) $this->amount / 100;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        if (null === $this->getComestible() || null === $this->amount) {
            return 0;
        }

        return $this->getComestible()->getFat() * (float) $this->amount / 100;
    }

    /**
     * @param float $amount
     *
     * @return ComestibleWithinDay
     */
    public function setAmount(float $amount): ComestibleWithinDay
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day->jsonSerialize(),
            'comestible' => $this->comestible->jsonSerialize(),
            'name' => $this->getName(),
            'calorie' => $this->getCalorie(),
            'protein' => $this->getProtein(),
            'carbohydrate' => $this->getCarbohydrate(),
            'fat' => $this->getFat(),
            'amount' => $this->amount,
        ];
    }
}
