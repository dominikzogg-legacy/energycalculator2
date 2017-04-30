<?php

namespace Energycalculator\Deserialize;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\DeserializeModel\Callback\ReferenceCallback;
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
     * @return string
     */
    public function getConstructMethod(): string
    {
        return 'create';
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('comestible', new ReferenceCallback($this->resolver, Comestible::class)),
            new PropertyMapping('amount'),
        ];
    }
}
