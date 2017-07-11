<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReference;
use Energycalculator\Model\Traits\IdTrait;
use Ramsey\Uuid\Uuid;

final class ComestibleWithinDay implements ModelInterface, \JsonSerializable
{
    use IdTrait;

    /**
     * @var string
     */
    private $dayId;

    /**
     * @var ModelReference
     */
    private $comestible;

    /**
     * @var float
     */
    private $amount = 0;

    /**
     * @param string|null $id
     *
     * @return ComestibleWithinDay
     */
    public static function create(string $id = null): ComestibleWithinDay
    {
        $comestibleWithinDay = new self();

        $comestibleWithinDay->id = $id ?? (string) Uuid::uuid4();
        $comestibleWithinDay->comestible = new ModelReference();

        return $comestibleWithinDay;
    }

    private function __construct()
    {
    }

    /**
     * @param Comestible $comestible
     *
     * @return ComestibleWithinDay
     */
    public function setComestible(Comestible $comestible): ComestibleWithinDay
    {
        $this->comestible = $comestible;

        return $this;
    }

    /**
     * @return Comestible|ModelInterface|null
     */
    public function getComestible()
    {
        return $this->comestible->getModel();
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (null === $this->getComestible()) {
            return '___NO_COMESTIBLE___';
        }

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
     * @param array $data
     *
     * @return Comestible|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $comestibleWithinDay = new self();

        $comestibleWithinDay->id = $data['id'];
        $comestibleWithinDay->dayId = $data['dayId'];
        $comestibleWithinDay->comestible = $data['comestible'];
        $comestibleWithinDay->amount = $data['amount'];

        return $comestibleWithinDay;
    }

    /**
     * @return array
     */
    public function toPersistence(): array
    {
        return [
            'id' => $this->id,
            'comestibleId' => $this->comestible->getId(),
            'dayId' => $this->dayId,
            'amount' => $this->amount,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
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
