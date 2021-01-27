<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Deserialization;

use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Energycalculator\Collection\DayCollection;

final class DayCollectionMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return DayCollection::class;
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
            return new DayCollection();
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
            DenormalizationFieldMappingBuilder::createConvertType('page', ConvertTypeFieldDenormalizer::TYPE_INT)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('perPage', ConvertTypeFieldDenormalizer::TYPE_INT)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('sort', ConvertTypeFieldDenormalizer::TYPE_INT)
                ->getMapping(),
        ];
    }
}
