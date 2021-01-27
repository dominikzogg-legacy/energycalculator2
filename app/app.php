<?php

declare(strict_types=1);

namespace Energycalculator;

use Chubbyphp\Lazy\LazyMiddleware;
use Energycalculator\Controller\HomeController;
use Energycalculator\Middleware\LocaleMiddleware;
use Energycalculator\ServiceProvider\ControllerServiceProvider;
use Energycalculator\ServiceProvider\MiddlewareServiceProvider;
use Slim\App;
use Slim\Container;
use Energycalculator\Controller\AuthController;
use Energycalculator\Controller\ComestibleController;
use Energycalculator\Controller\UserController;
use Energycalculator\Controller\ChartController;

require __DIR__.'/bootstrap.php';

function prepareForView($value)
{
    return json_decode(json_encode($value), true);
}

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ControllerServiceProvider());
$container->register(new MiddlewareServiceProvider());

$app = new App($container);

$app->add(new LazyMiddleware($container, 'csrf.middleware'));
$app->add(new LazyMiddleware($container, 'session.middleware'));
$app->add(new LazyMiddleware($container, LocaleMiddleware::class));

$app->group('/{locale:'.implode('|', $container['locales']).'}', function () use ($app, $container) {
    $app->get('', HomeController::class.':home')->setName('home');
    $app->post('/login', AuthController::class.':login')->setName('login');
    $app->post('/logout', AuthController::class.':logout')->setName('logout');
    $app->group('/comestibles', function () use ($app, $container) {
        $app->get('', 'comestible.controller.list')->setName('comestible_list');
        $app->map(['GET', 'POST'], '/create', 'comestible.controller.create')->setName('comestible_create');
        $app->get('/{id}/read', 'comestible.controller.read')->setName('comestible_read');
        $app->map(['GET', 'POST'], '/{id}/update', 'comestible.controller.update')->setName('comestible_update');
        $app->post('/{id}/delete', 'comestible.controller.delete')->setName('comestible_delete');
        $app->get('/findbynamelike', ComestibleController::class.':findByNameLike')->setName('comestible_findbynamelike');
    })->add($container['security.authentication.middleware']);
    $app->group('/days', function () use ($app, $container) {
        $app->get('', 'day.controller.list')->setName('day_list');
        $app->map(['GET', 'POST'], '/create', 'day.controller.create')->setName('day_create');
        $app->get('/{id}/read', 'day.controller.read')->setName('day_read');
        $app->map(['GET', 'POST'], '/{id}/update', 'day.controller.update')->setName('day_update');
        $app->post('/{id}/delete', 'day.controller.delete')->setName('day_delete');
    })->add($container['security.authentication.middleware']);
    $app->group('/users', function () use ($app, $container) {
        $app->get('', UserController::class.':listAll')->setName('user_list');
        $app->map(['GET', 'POST'], '/create', UserController::class.':create')->setName('user_create');
        $app->get('/{id}/read', UserController::class.':read')->setName('user_read');
        $app->map(['GET', 'POST'], '/{id}/update', UserController::class.':update')->setName('user_update');
        $app->post('/{id}/delete', UserController::class.':delete')->setName('user_delete');
    })->add($container['security.authentication.middleware']);
    $app->group('/chart', function () use ($app, $container) {
        $app->get('/weight', ChartController::class.':weight')->setName('chart_weight');
        $app->get('/calorie', ChartController::class.':calorie')->setName('chart_calorie');
        $app->get('/energymix', ChartController::class.':energymix')->setName('chart_energymix');
    })->add($container['security.authentication.middleware']);
});

return $app;
