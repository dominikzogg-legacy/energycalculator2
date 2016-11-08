<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Rules\FloatVal;
use Respect\Validation\Validator as v;

final class Comestible implements \JsonSerializable, OwnedByUserModelInterface, ValidatableModelInterface
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
     * @return Comestible
     */
    public function withUpdatedAt(\DateTime $updatedAt): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $updatedAt, $this->updatedAt);
        $comestible->updatedAt = $updatedAt;

        return $comestible;
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
     * @return Comestible
     */
    public function withUser(User $user): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $user->getId(), $this->userId);
        $comestible->user = $user;
        $comestible->userId = $user->getId();

        return $comestible;
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
     * @param string $name
     *
     * @return Comestible
     */
    public function withName(string $name): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $name, $this->name);
        $comestible->name = $name;

        return $comestible;
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
     *
     * @return Comestible
     */
    public function withCalorie(float $calorie): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $calorie, $this->calorie);
        $comestible->calorie = $calorie;

        return $comestible;
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
     *
     * @return Comestible
     */
    public function withProtein(float $protein): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $protein, $this->protein);
        $comestible->protein = $protein;

        return $comestible;
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
     *
     * @return Comestible
     */
    public function withCarbohydrate(float $carbohydrate): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $carbohydrate, $this->carbohydrate);
        $comestible->carbohydrate = $carbohydrate;

        return $comestible;
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
     *
     * @return Comestible
     */
    public function withFat(float $fat): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $fat, $this->fat);
        $comestible->fat = $fat;

        return $comestible;
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
     *
     * @return Comestible
     */
    public function withDefaultValue(float $defaultValue = null): Comestible
    {
        $comestible = $this->cloneWithModification(__METHOD__, $defaultValue, $this->defaultValue);
        $comestible->defaultValue = $defaultValue;

        return $comestible;
    }

    /**
     * @return float|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param string $method
     * @param mixed  $new
     * @param mixed  $old
     *
     * @return Comestible
     */
    private function cloneWithModification(string $method, $new, $old): Comestible
    {
        $comestible = clone $this;
        $comestible->__modifications[] = [
            'method' => $method,
            'new' => $new,
            'old' => $old,
        ];

        return $comestible;
    }

    /**
     * @param array $data
     *
     * @return Comestible|ModelInterface
     */
    public static function fromRow(array $data): ModelInterface
    {
        $comestible = new self($data['id'], new \DateTime($data['createdAt']));

        $comestible->updatedAt = null !== $data['updatedAt'] ? new \DateTime($data['updatedAt']) : null;
        $comestible->user = $data['user'];
        $comestible->userId = $data['userId'];
        $comestible->name = $data['name'];
        $comestible->calorie = $data['calorie'];
        $comestible->protein = $data['protein'];
        $comestible->carbohydrate = $data['carbohydrate'];
        $comestible->fat = $data['fat'];
        $comestible->defaultValue = $data['defaultValue'];

        return $comestible;
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
            'name' => $this->name,
            'calorie' => $this->calorie,
            'protein' => $this->protein,
            'carbohydrate' => $this->carbohydrate,
            'fat' => $this->fat,
            'defaultValue' => $this->defaultValue,
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
            'name' => $this->name,
            'calorie' => $this->calorie,
            'protein' => $this->protein,
            'carbohydrate' => $this->carbohydrate,
            'fat' => $this->fat,
            'defaultValue' => $this->defaultValue,
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
            'user' => v::notEmpty(),
            'name' => v::notBlank(),
            'calorie' => v::floatVal(),
            'protein' => v::floatVal(),
            'carbohydrate' => v::floatVal(),
            'fat' => v::floatVal(),
            'defaultValue' => v::optional(new FloatVal()),
        ];
    }
}
