<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Energycalculator\Model\Traits\CloneWithModificationTrait;
use Energycalculator\Model\Traits\CreatedAndUpdatedAtTrait;
use Energycalculator\Model\Traits\IdTrait;
use Respect\Validation\Validator as v;

final class ComestibleWithinDay implements ValidatableModelInterface
{
    use CloneWithModificationTrait;
    use CreatedAndUpdatedAtTrait;
    use IdTrait;

    /**
     * @var string
     */
    private $dayId;

    /**
     * @var Comestible|\Closure|null
     */
    private $comestible;

    /**
     * @var string
     */
    private $comestibleId;

    /**
     * @var float
     */
    private $amount = 0;

    /**
     * @param string $id
     * @param \DateTime $createdAt
     * @param string $dayId
     */
    public function __construct(string $id, \DateTime $createdAt, string $dayId)
    {
        $this->id = $id;
        $this->setCreatedAt($createdAt);
        $this->dayId = $dayId;
    }

    /**
     * @param Comestible $comestible
     *
     * @return ComestibleWithinDay
     */
    public function withComestible(Comestible $comestible): ComestibleWithinDay
    {
        $comestibleWithinDay = $this->cloneWithModification(__METHOD__, $comestible->getId(), $this->comestibleId);
        $comestibleWithinDay->comestible = $comestible;
        $comestibleWithinDay->comestibleId = $comestible->getId();

        return $comestibleWithinDay;
    }

    /**
     * @return Comestible|null
     */
    public function getComestible()
    {
        if ($this->comestible instanceof \Closure) {
            $comestible = $this->comestible;
            $this->comestible = $comestible();
        }

        return $this->comestible;
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
    public function withAmount(float $amount): ComestibleWithinDay
    {
        $comestibleWithinDay = $this->cloneWithModification(__METHOD__, $amount, $this->amount);
        $comestibleWithinDay->amount = $amount;

        return $comestibleWithinDay;
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
        $comestibleWithinDay = new self($data['id'], new \DateTime($data['createdAt']), $data['dayId']);

        $comestibleWithinDay->updatedAt = $data['updatedAt'];
        $comestibleWithinDay->comestible = $data['comestible'];
        $comestibleWithinDay->comestibleId = $data['comestibleId'];
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
            'comestibleId' => $this->comestibleId,
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
            'comestible' => null !== $this->comestibleId ? $this->getComestible()->jsonSerialize() : null,
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
        return v::create()->addRule(new UniqueModelRule(['userId', 'name']));
    }

    /**
     * @return v[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'comestibleId' => v::notBlank(),
            'amount' => v::floatVal(),
        ];
    }
}
