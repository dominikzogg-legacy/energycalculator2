<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class MonologServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['monolog'] = function () use ($container) {
            return new Logger($container['monolog.name'], [
                new StreamHandler(
                    $container['monolog.path'],
                    self::translateLevel($container['monolog.level'])
                ),
            ]);
        };

        $container['logger'] = function () use ($container) {
            return $container['monolog'];
        };
    }

    /**
     * @param string|int $name
     *
     * @return int
     *
     * @throws \LogicException
     */
    public static function translateLevel($name): int
    {
        if (is_int($name)) {
            return $name;
        }

        $levels = Logger::getLevels();
        $upper = strtoupper($name);

        if (!isset($levels[$upper])) {
            throw new \LogicException(
                sprintf('Invalid log level: "%s"', $name)
            );
        }

        return $levels[$upper];
    }
}
