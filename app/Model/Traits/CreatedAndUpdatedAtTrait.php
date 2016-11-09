<?php

namespace Energycalculator\Model\Traits;

trait CreatedAndUpdatedAtTrait
{
    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string|null
     */
    private $updatedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return new \DateTime($this->createdAt);
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function withUpdatedAt(\DateTime $updatedAt)
    {
        $updatedAt = $updatedAt->format('Y-m-d H:i:s');
        $model = $this->cloneWithModification(__METHOD__, $updatedAt, $this->updatedAt);
        $model->updatedAt = $updatedAt;

        return $model;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        if (null === $this->updatedAt) {
            return null;
        }

        return new \DateTime($this->updatedAt);
    }
}
