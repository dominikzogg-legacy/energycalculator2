<?php

declare(strict_types=1);

namespace Energycalculator\Factory;

use Energycalculator\Collection\CollectionInterface;

interface CollectionFactoryInterface
{
    /**
     * @return CollectionInterface
     */
    public function create(): CollectionInterface;
}
