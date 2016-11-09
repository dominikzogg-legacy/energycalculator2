<?php

namespace Energycalculator\Model;

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

final class Comestible implements \JsonSerializable, OwnedByUserModelInterface, ValidatableModelInterface
{
    use CloneWithModificationTrait;
    use CreatedAndUpdatedAtTrait;
    use IdTrait;
    use OwnedByUserTrait;

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
     * @param string|null    $id
     * @param \DateTime|null $createdAt
     */
    public function __construct(string $id = null, \DateTime $createdAt = null)
    {
        $this->id = $id ?? (string) Uuid::uuid4();
        $this->createdAt = ($createdAt ?? new \DateTime())->format('Y-m-d H:i:s');
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
     * @param array $data
     *
     * @return Comestible|ModelInterface
     */
    public static function fromRow(array $data): ModelInterface
    {
        $comestible = new self($data['id'], new \DateTime($data['createdAt']));

        $comestible->updatedAt = $data['updatedAt'];
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'user' => null !== $this->userId ? $this->getUser()->jsonSerialize(): null,
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
