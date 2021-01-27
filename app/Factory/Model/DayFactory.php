<?php

declare(strict_types=1);

namespace Energycalculator\Factory\Model;

use Energycalculator\Factory\ModelFactoryInterface;
use Energycalculator\Model\ModelInterface;
use Energycalculator\Model\Day;

final class DayFactory implements ModelFactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface
    {
        return new Day();
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return Day::class;
    }
}
