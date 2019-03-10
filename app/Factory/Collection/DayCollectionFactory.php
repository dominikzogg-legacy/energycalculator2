<?php

declare(strict_types=1);

namespace Energycalculator\Factory\Collection;

use Energycalculator\Collection\CollectionInterface;
use Energycalculator\Collection\DayCollection;
use Energycalculator\Factory\CollectionFactoryInterface;

final class DayCollectionFactory implements CollectionFactoryInterface
{
    /**
     * @return CollectionInterface
     */
    public function create(): CollectionInterface
    {
        return new DayCollection();
    }
}
