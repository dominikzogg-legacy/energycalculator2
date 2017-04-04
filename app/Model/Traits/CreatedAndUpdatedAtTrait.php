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
     * @param \DateTime $createdAt
     */
    private function setCreatedAt(\DateTime $createdAt)
    {
        $createdAt = $createdAt->format('Y-m-d H:i:s');
        $this->createdAt = $createdAt;
    }

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
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt->format('Y-m-d H:i:s');

        return $this;
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
