<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Model\Collection\ModelCollectionInterface;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Energycalculator\Model\Traits\CloneWithModificationTrait;
use Energycalculator\Model\Traits\CreatedAndUpdatedAtTrait;
use Energycalculator\Model\Traits\IdTrait;
use Energycalculator\Model\Traits\OwnedByUserTrait;
use Ramsey\Uuid\Uuid;
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
     * @param string|null $id
     * @param \DateTime|null $createdAt
     */
    public function __construct(string $id = null, \DateTime $createdAt = null)
    {
        $this->id = $id ?? (string)Uuid::uuid4();
        $this->setCreatedAt($createdAt ?? new \DateTime());
        $this->comestiblesWithinDay = new ModelCollection();
    }

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
     * @param ComestibleWithinDay $comestibleWithinDay
     * @return Day
     */
    public function addComestibleWithinDay(ComestibleWithinDay $comestibleWithinDay): Day
    {
        $comestibleWithinDay = $comestibleWithinDay->withDay($this);
        $this->comestiblesWithinDay->add($comestibleWithinDay);

        return $this;
    }

    /**
     * @param ComestibleWithinDay $comestibleWithinDay
     * @return Day
     */
    public function removeComestibleWithinDay(ComestibleWithinDay $comestibleWithinDay): Day
    {
        $comestibleWithinDay = $comestibleWithinDay->withDay($this);
        $this->comestiblesWithinDay->remove($comestibleWithinDay);

        return $this;
    }

    /**
     * @param array $comestiblesWithinDay
     * @return Day
     */
    public function setComestiblesWithinDay(array $comestiblesWithinDay): Day
    {
        foreach ($this->comestiblesWithinDay as $comestibleWithinDay) {
            $this->removeComestibleWithinDay($comestibleWithinDay);
        }

        foreach ($comestiblesWithinDay as $comestibleWithinDay) {
            $this->addComestibleWithinDay($comestibleWithinDay);
        }

        return $this;
    }

    /**
     * @return ComestibleWithinDay[]|array
     */
    public function getComestiblesWithinDay(): array
    {
        $comestiblesWithinDay = [];
        foreach ($this->comestiblesWithinDay as $comestibleWithinDay) {
            $comestiblesWithinDay[] = $comestibleWithinDay;
        }

        return $comestiblesWithinDay;
    }

    /**
     * @param array $data
     *
     * @return Day|ModelInterface
     */
    public static function fromRow(array $data): ModelInterface
    {
        $day = new self($data['id'], new \DateTime($data['createdAt']));

        $day->updatedAt = $data['updatedAt'];
        $day->user = $data['user'];
        $day->userId = $data['userId'];
        $day->date = $data['date'];
        $day->weight = $data['weight'];
        $day->comestiblesWithinDay = $data['comestiblesWithinDay'];

        return $day;
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
            'userId' => $this->userId,
            'date' => $this->date,
            'weight' => $this->weight,
            'comestiblesWithinDay' => $this->comestiblesWithinDay
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
            'user' => null !== $this->userId ? $this->getUser()->jsonSerialize() : null,
            'date' => $this->date,
            'weight' => $this->weight,
            'comestiblesWithinDay' => $this->comestiblesWithinDay->jsonSerialize()
        ];
    }

    /**
     * @return v|null
     */
    public function getModelValidator()
    {
        return v::create()->addRule(new UniqueModelRule(['userId', 'date']));
    }

    /**
     * @return v[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'user' => v::notEmpty(),
            'date' => v::date(),
            'weight' => v::optional(new FloatVal()),
        ];
    }
}
