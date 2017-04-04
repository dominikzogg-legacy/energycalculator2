<?php

namespace Energycalculator\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authentication\UserPasswordInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Energycalculator\Model\Traits\CreatedAndUpdatedAtTrait;
use Energycalculator\Model\Traits\IdTrait;
use Respect\Validation\Validator as v;

final class User implements UserPasswordInterface, ValidatableModelInterface
{
    use CreatedAndUpdatedAtTrait;
    use IdTrait;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $roles;

    /**
     * @param string $id
     * @param \DateTime $createdAt
     * @return User
     */
    public static function create(string $id, \DateTime $createdAt): User
    {
        $user = new self();
        $user->id = $id;
        $user->setCreatedAt($createdAt);

        return $user;
    }

    private function __construct() {}

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        $this->username = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return User|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $user = new self();

        $user->id = $data['id'];
        $user->createdAt = $data['createdAt'];
        $user->updatedAt = $data['updatedAt'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->roles = json_decode($data['roles'], true);

        return $user;
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
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => json_encode($this->roles),
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
            'username' => $this->username,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }

    /**
     * @return v|null
     */
    public function getModelValidator()
    {
        return v::create()->addRule(new UniqueModelRule(['username', 'email']));
    }

    /**
     * @return v[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'username' => v::notBlank()->email(),
            'email' => v::notBlank()->email(),
            'password' => v::notBlank(),
            'roles' => v::notEmpty(),
        ];
    }
}
