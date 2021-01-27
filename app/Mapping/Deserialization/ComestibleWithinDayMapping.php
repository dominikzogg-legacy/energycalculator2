<?php

declare(strict_types=1);

namespace Energycalculator\Mapping\Deserialization;

use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Energycalculator\Model\ComestibleWithinDay;
use Energycalculator\Repository\RepositoryInterface;

final class ComestibleWithinDayMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var RepositoryInterface
     */
    private $comestibleRepository;

    /**
     * @param RepositoryInterface $comestibleRepository
     */
    public function __construct(RepositoryInterface $comestibleRepository)
    {
        $this->name = $comestibleRepository;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return ComestibleWithinDay::class;
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
            return new ComestibleWithinDay();
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
            DenormalizationFieldMappingBuilder::createReferenceOne('comestible', [$this->comestibleRepository, 'find'], true)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('amount', ConvertTypeFieldDenormalizer::TYPE_FLOAT, true)
                ->getMapping(),
        ];
    }
}
