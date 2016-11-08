<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Rules\FloatVal;
use Respect\Validation\Validator as v;

final class Day implements \JsonSerializable, OwnedByUserModelInterface, ValidatableModelInterface
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
     * @var User|\Closure|null
     */
    private $user;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $date;

    /**
     * @var float|null
     */
    private $weight;

    /**
     * @var array
     */
    private $__modifications = [];

    /**
     * @param string|null    $id
     * @param \DateTime|null $createdAt
     */
    public function __construct(string $id = null, \DateTime $createdAt = null)
    {
        $this->id = $id ?? (string) Uuid::uuid4();
        $this->createdAt = $createdAt ?? new \DateTime();
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
     *
     * @return Day
     */
    public function withUpdatedAt(\DateTime $updatedAt): Day
    {
        $day = $this->cloneWithModification(__METHOD__, $updatedAt, $this->updatedAt);
        $day->updatedAt = $updatedAt;

        return $day;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @param User $user
     *
     * @return Day
     */
    public function withUser(User $user): Day
    {
        $day = $this->cloneWithModification(__METHOD__, $user->getId(), $this->userId);
        $day->user = $user;
        $day->userId = $user->getId();

        return $day;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user instanceof \Closure) {
            $user = $this->user;
            $this->user = $user();
        }

        return $this->user;
    }

    /**
     * @return string
     */
    public function getOwnedByUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param \DateTime $date
     *
     * @return Day
     */
    public function withDate(\DateTime $date): Day
    {
        $day = $this->cloneWithModification(__METHOD__, $date->format('Y-m-d'), $this->date);
        $day->date = $date->format('Y-m-d');

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
     * @param string $method
     * @param mixed  $new
     * @param mixed  $old
     *
     * @return Day
     */
    private function cloneWithModification(string $method, $new, $old): Day
    {
        $day = clone $this;
        $day->__modifications[] = [
            'method' => $method,
            'new' => $new,
            'old' => $old,
        ];

        return $day;
    }

    /**
     * @param array $data
     *
     * @return Day|ModelInterface
     */
    public static function fromRow(array $data): ModelInterface
    {
        $day = new self($data['id'], new \DateTime($data['createdAt']));

        $day->updatedAt = null !== $data['updatedAt'] ? new \DateTime($data['updatedAt']) : null;
        $day->user = $data['user'];
        $day->userId = $data['userId'];
        $day->date = $data['date'];
        $day->weight = $data['weight'];

        return $day;
    }

    /**
     * @return array
     */
    public function toRow(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => null !== $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null,
            'userId' => $this->userId,
            'date' => $this->date,
            'weight' => $this->weight
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => null !== $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null,
            'user' => $this->getUser(),
            'date' => $this->date,
            'weight' => $this->weight,
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
            'date' => v::date('Y-m-d'),
            'weight' => v::optional(new FloatVal()),
        ];
    }
}
