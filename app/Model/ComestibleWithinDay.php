<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReference;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Energycalculator\Model\Traits\CreatedAndUpdatedAtTrait;
use Energycalculator\Model\Traits\IdTrait;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

final class ComestibleWithinDay implements ValidatableModelInterface
{
    use CreatedAndUpdatedAtTrait;
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
     * @return ComestibleWithinDay
     */
    public static function create(string $id = null): ComestibleWithinDay
    {
        $comestibleWithinDay = new self();

        $comestibleWithinDay->id = $id ?? Uuid::uuid4();
        $comestibleWithinDay->setCreatedAt(new \DateTime());
        $comestibleWithinDay->comestible = new ModelReference();

        return $comestibleWithinDay;
    }

    private function __construct() {}

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
        if (null === $this->getComestible()) {
            return 0;
        }

        return $this->getComestible()->getCalorie() * $this->getAmount() / 100;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        if (null === $this->getComestible()) {
            return 0;
        }

        return $this->getComestible()->getProtein() * $this->getAmount() / 100;
    }

    /**
     * @return float
     */
    public function getCarbohydrate()
    {
        if (null === $this->getComestible()) {
            return 0;
        }

        return $this->getComestible()->getCarbohydrate() * $this->getAmount() / 100;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        if (null === $this->getComestible()) {
            return 0;
        }

        return $this->getComestible()->getFat() * $this->getAmount() / 100;
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
        $comestibleWithinDay->createdAt = $data['createdAt'];
        $comestibleWithinDay->updatedAt = $data['updatedAt'];
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'comestible' => $this->comestible->jsonSerialize(),
            'name' => $this->getName(),
            'calorie' => $this->getCalorie(),
            'protein' => $this->getProtein(),
            'carbohydrate' => $this->getCarbohydrate(),
            'fat' => $this->getFat(),
            'amount' => $this->amount,
        ];
    }

    /**
     * @return v|null
     */
    public function getModelValidator()
    {
        return v::create()->addRule(new UniqueModelRule(['dayId', 'name']));
    }

    /**
     * @return v[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'comestible' => v::notBlank(),
            'amount' => v::floatVal(),
        ];
    }
}
