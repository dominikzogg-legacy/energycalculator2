<?php

declare(strict_types=1);

namespace Energycalculator\Collection;

final class DayCollection extends AbstactOwnedByUserCollection
{
    /**
     * @var int
     */
    protected $perPage = 7;
}
