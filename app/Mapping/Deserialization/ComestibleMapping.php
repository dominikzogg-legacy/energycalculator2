<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Deserialization;

use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Energycalculator\Model\Comestible;

final class ComestibleMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Comestible::class;
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
            return new Comestible();
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
            DenormalizationFieldMappingBuilder::create('name', true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('calorie', ConvertTypeFieldDenormalizer::TYPE_FLOAT, true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('protein', ConvertTypeFieldDenormalizer::TYPE_FLOAT, true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('carbohydrate', ConvertTypeFieldDenormalizer::TYPE_FLOAT, true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('fat', ConvertTypeFieldDenormalizer::TYPE_FLOAT, true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('defaultValue', ConvertTypeFieldDenormalizer::TYPE_FLOAT, true)
                ->getMapping(),
        ];
    }
}
