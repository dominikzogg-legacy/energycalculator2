<?php

declare(strict_types=1);

namespace Energycalculator\Model;

interface ModelInterface extends \JsonSerializable
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void;

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime;

    /**
     * @return array
     */
    public function jsonSerialize(): array;
}
