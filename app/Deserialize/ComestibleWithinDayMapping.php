<?php

namespace Energycalculator\Deserialize;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\DeserializeModel\Deserializer\PropertyModelRefenceDeserializer;
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
            new PropertyMapping('comestible', new PropertyModelRefenceDeserializer($this->resolver, Comestible::class)),
            new PropertyMapping('amount'),
        ];
    }
}
