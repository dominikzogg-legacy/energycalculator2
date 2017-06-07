<?php

namespace Energycalculator\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Chubbyphp\DeserializationModel\Deserializer\PropertyModelReferenceDeserializer;
use Chubbyphp\Model\ResolverInterface;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;

class ComestibleWithinDayMapping implements ObjectMappingInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return ComestibleWithinDay::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [ComestibleWithinDay::class, 'create'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('comestible', new PropertyModelReferenceDeserializer($this->resolver, Comestible::class)),
            new PropertyMapping('amount'),
        ];
    }
}
