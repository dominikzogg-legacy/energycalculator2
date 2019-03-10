<?php

declare(strict_types=1);

namespace Energycalculator\Factory\Model;

use Energycalculator\Factory\ModelFactoryInterface;
use Energycalculator\Model\ModelInterface;
use Energycalculator\Model\Comestible;

final class ComestibleFactory implements ModelFactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface
    {
        return new Comestible();
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return Comestible::class;
    }
}
