<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Deserialization;

use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Model\Day;

final class DayMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Day::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(
        string $path,
        string $type = null
    ): callable {
        return function () {
            return new Day();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(
        string $path,
        string $type = null
    ): array {
        return [
            DenormalizationFieldMappingBuilder::createDateTime('date')
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('weight', ConvertTypeFieldDenormalizer::TYPE_FLOAT, true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createEmbedMany('comestiblesWithinDay', ComestibleWithinDay::class)
                ->getMapping(),
        ];
    }
}
