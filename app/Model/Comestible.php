<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReference;
use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Energycalculator\Model\Traits\IdTrait;
use Energycalculator\Model\Traits\OwnedByUserTrait;
use Ramsey\Uuid\Uuid;

final class Comestible implements ModelInterface, OwnedByUserModelInterface, \JsonSerializable
{
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
     * @param string|null $id
     *
     * @return Comestible
     */
    public static function create(string $id = null): Comestible
    {
        $comestible = new self();

        $comestible->id = $id ?? (string) Uuid::uuid4();
        $comestible->user = new ModelReference();

        return $comestible;
    }

    private function __construct()
    {
    }

    /**
     * @param string $name
     *
     * @return Comestible
     */
    public function setName(string $name): Comestible
    {
        $this->name = $name;

        return $this;
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
    public function setCalorie(float $calorie): Comestible
    {
        $this->calorie = $calorie;

        return $this;
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
    public function setProtein(float $protein): Comestible
    {
        $this->protein = $protein;

        return $this;
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
    public function setCarbohydrate(float $carbohydrate): Comestible
    {
        $this->carbohydrate = $carbohydrate;

        return $this;
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
    public function setFat(float $fat): Comestible
    {
        $this->fat = $fat;

        return $this;
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
    public function setDefaultValue(float $defaultValue = null): Comestible
    {
        $this->defaultValue = $defaultValue;

        return $this;
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
    public static function fromPersistence(array $data): ModelInterface
    {
        $comestible = new self();

        $comestible->id = $data['id'];
        $comestible->user = $data['user'];
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
    public function toPersistence(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user->getId(),
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
