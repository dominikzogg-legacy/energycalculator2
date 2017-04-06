<?php

namespace Energycalculator\Deserializer;

use Chubbyphp\Deserialize\Callback\OneToManyCallback;
use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;

class DayMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Day::class;
    }

    /**
     * @return string
     */
    public function getConstructMethod(): string
    {
        return 'create';
    }

    /**
     * @return array
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('date'),
            new PropertyMapping('weight'),
            new PropertyMapping('comestiblesWithinDay', new OneToManyCallback(ComestibleWithinDay::class)),
        ];
    }
}
