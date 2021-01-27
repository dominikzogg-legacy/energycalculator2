<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Energycalculator\Csrf\CsrfErrorHandler;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TwigRender;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Chubbyphp\Translation\LocaleTranslationProvider;

final class GenericServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[RedirectForPath::class] = function () use ($container) {
            return new RedirectForPath($container['router']);
        };

        $container[ErrorResponseHandler::class] = function () use ($container) {
            return new ErrorResponseHandler($container[TwigRender::class]);
        };

        $container['csrf.errorResponseHandler'] = function () use ($container) {
            return new CsrfErrorHandler($container['session']);
        };

        $container->extend('translator.providers', function (array $providers) use ($container) {
            $providers[] = new LocaleTranslationProvider(
                'de',
                require $container['rootDir'].'/translations/de.php'
            );
            $providers[] = new LocaleTranslationProvider(
                'en',
                require $container['rootDir'].'/translations/en.php'
            );

            return $providers;
        });

        $container[TwigRender::class] = function () use ($container) {
            return new TwigRender(
                $container['security.authentication'],
                $container['debug'],
                $container['session'],
                [
                    'chart_calorie' => ['charts'],
                    'chart_energymix' => ['charts'],
                    'chart_weight' => ['charts'],
                    'charts' => [],
                    'comestible_create' => ['comestible_list'],
                    'comestible_delete' => ['comestible_list'],
                    'comestible_update' => ['comestible_list'],
                    'comestible_list' => [],
                    'comestible_read' => ['comestible_list'],
                    'day_create' => ['day_list'],
                    'day_delete' => ['day_list'],
                    'day_update' => ['day_list'],
                    'day_list' => [],
                    'day_read' => ['day_list'],
                    'user_create' => ['user_list'],
                    'user_delete' => ['user_list'],
                    'user_update' => ['user_list'],
                    'user_list' => [],
                    'user_read' => ['user_list'],
                ],
                $container['translator'],
                $container['twig']
            );
        };
    }
}
