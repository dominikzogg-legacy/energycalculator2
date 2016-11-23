<?php

use bitExpert\Http\Middleware\Psr7\Prophiler\ProphilerMiddleware;
use Chubbyphp\Lazy\LazyMiddleware;
use Negotiation\LanguageNegotiator;
use Slim\App;
use Slim\Container;
use Energycalculator\Middleware\LocaleMiddleware;

/* @var App $app */
/* @var Container $container */

$container[LocaleMiddleware::class] = function () use ($container) {
    return new LocaleMiddleware(
        $container[LanguageNegotiator::class],
        $container['localeFallback'],
        $container['locales']
    );
};

$app->add(new LazyMiddleware($container, 'csrf.middleware'));
$app->add(new LazyMiddleware($container, 'session.middleware'));
$app->add(new LazyMiddleware($container, LocaleMiddleware::class));
$app->add(new LazyMiddleware($container, 'errorHandler.middleware'));

if ($container['debug']) {
    $app->add(new LazyMiddleware($container, ProphilerMiddleware::class));
}
