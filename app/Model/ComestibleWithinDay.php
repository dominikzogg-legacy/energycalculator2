<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Energycalculator\Model\Traits\CloneWithModificationTrait;
use Energycalculator\Model\Traits\CreatedAndUpdatedAtTrait;
use Energycalculator\Model\Traits\IdTrait;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

final class ComestibleWithinDay implements ValidatableModelInterface
{
    use CloneWithModificationTrait;
    use CreatedAndUpdatedAtTrait;
    use IdTrait;

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
     * @param string|null    $id
     * @param \DateTime|null $createdAt
     */
    public function __construct(string $id = null, \DateTime $createdAt = null)
    {
        $this->id = $id ?? (string) Uuid::uuid4();
        $this->setCreatedAt($createdAt ?? new \DateTime());
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
        $comestibleWithinDay->comestibleId = $comestibleWithinDay->getId();

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
    public static function fromRow(array $data): ModelInterface
    {
        $comestibleWithinDay = new self($data['id'], new \DateTime($data['createdAt']));

        $comestibleWithinDay->updatedAt = $data['updatedAt'];
        $comestibleWithinDay->comestible = $data['comestible'];
        $comestibleWithinDay->comestibleId = $data['comestibleId'];
        $comestibleWithinDay->amount = $data['amount'];

        return $comestibleWithinDay;
    }

    /**
     * @return array
     */
    public function toRow(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'comestibleId' => $this->comestibleId,
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
            'user' => null !== $this->comestibleId ? $this->getComestible()->jsonSerialize(): null,
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
            'comestible' => v::notEmpty(),
            'amount' => v::floatVal(),
        ];
    }
}
