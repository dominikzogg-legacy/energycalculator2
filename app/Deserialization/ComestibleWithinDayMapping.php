<?php

declare(strict_types=1);

namespace Energycalculator\Deserialization;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerCallback;
use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Chubbyphp\DeserializationModel\Deserializer\PropertyModelReferenceDeserializer;
use Chubbyphp\Model\ResolverInterface;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\ComestibleWithinDay;

final class ComestibleWithinDayMapping implements ObjectMappingInterface
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
            new PropertyMapping(
                'sorting',
                new PropertyDeserializerCallback(
                    function ($path) {
                        $pathParts = $this->pathParts($path);
                        array_pop($pathParts);
                        $comestibleWithinDayPart = array_pop($pathParts);

                        return $comestibleWithinDayPart['index'];
                    }
                )
            ),
            new PropertyMapping('comestible', new PropertyModelReferenceDeserializer($this->resolver, Comestible::class)),
            new PropertyMapping('amount'),
        ];
    }

    /**
     * @param string $path
     *
     * @return array
     */
    private function pathParts(string $path): array
    {
        $pathParts = [];
        foreach (explode('.', $path) as $rawPathPart) {
            $matches = [];
            if (1 === preg_match('/(.+)\[(\d+)\]/', $rawPathPart, $matches)) {
                $pathParts[] = ['property' => $matches[1], 'index' => (int) $matches[2]];
            } else {
                $pathParts[] = ['property' => $rawPathPart];
            }
        }

        return $pathParts;
    }
}
