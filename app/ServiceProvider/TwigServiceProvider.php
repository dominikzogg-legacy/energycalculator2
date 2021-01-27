<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Energycalculator\Twig\NumericExtension;
use Energycalculator\Twig\RouterExtension;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Chubbyphp\Translation\TranslationTwigExtension;

final class TwigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['twig.namespaces'] = function () use ($container) {
            return [
                'Energycalculator' => $container['rootDir'].'/views',
            ];
        };

        $container['twig.extensions'] = function () use ($container) {
            $extensions[] = new NumericExtension();
            $extensions[] = new RouterExtension($container['router']);
            $extensions[] = new TranslationTwigExtension($container['translator']);
            if ($container['debug']) {
                $extensions[] = new \Twig_Extension_Debug();
            }

            return $extensions;
        };

        $container['twig.globals'] = function () use ($container) {
            return [];
        };

        $container['twig'] = function () use ($container) {
            $twig = new \Twig_Environment($container['twig.loader'], [
                'cache' => !$container['debug'] ? $container['cacheDir'].'/twig' : false,
                'debug' => $container['debug'],
            ]);

            foreach ($container['twig.extensions'] as $extension) {
                $twig->addExtension($extension);
            }

            foreach ($container['twig.globals'] as $name => $value) {
                $twig->addGlobal($name, $value);
            }

            return $twig;
        };

        $container['twig.loader'] = function () use ($container) {
            $loader = new \Twig_Loader_Filesystem();
            foreach ($container['twig.namespaces'] as $namespace => $path) {
                $loader->addPath($path, $namespace);
            }

            return $loader;
        };
    }
}
