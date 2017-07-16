<?php

namespace Energycalculator\Model;

final class DateRange
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @param \DateTime $from
     *
     * @return self
     */
    public function setFrom(\DateTime $from): self
    {
        $this->from = $from->format('Y-m-d');

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): \DateTime
    {
        return new \DateTime($this->from);
    }

    /**
     * @param \DateTime $to
     *
     * @return self
     */
    public function setTo(\DateTime $to): self
    {
        $this->to = $to->format('Y-m-d');

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTo(): \DateTime
    {
        return new \DateTime($this->to);
    }
}
