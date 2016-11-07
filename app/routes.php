<?php

use Slim\App;
use Slim\Container;
use Energycalculator\Controller\AuthController;
use Energycalculator\Controller\ComestibleController;
use Energycalculator\Controller\HomeController;
use Energycalculator\Controller\UserController;

/* @var App $app */
/* @var Container $container */

$app->group('/{locale:'.implode('|', $container['locales']).'}', function () use ($app, $container) {
    $app->get('', HomeController::class.':home')->setName('home');

    $app->post('/login', AuthController::class.':login')->setName('login');
    $app->post('/logout', AuthController::class.':logout')->setName('logout');

    $app->group('/comestibles', function () use ($app, $container) {
        $app->get('', ComestibleController::class.':listAll')->setName('comestible_list');
        $app->map(['GET', 'POST'], '/create', ComestibleController::class.':create')->setName('comestible_create');
        $app->map(['GET', 'POST'], '/{id}/edit', ComestibleController::class.':edit')->setName('comestible_edit');
        $app->get('/{id}/view', ComestibleController::class.':view')->setName('comestible_view');
        $app->post('/{id}/delete', ComestibleController::class.':delete')->setName('comestible_delete');
    })->add($container['security.authentication.middleware']);

    $app->group('/users', function () use ($app, $container) {
        $app->get('', UserController::class.':listAll')->setName('user_list');
        $app->map(['GET', 'POST'], '/create', UserController::class.':create')->setName('user_create');
        $app->map(['GET', 'POST'], '/{id}/edit', UserController::class.':edit')->setName('user_edit');
        $app->get('/{id}/view', UserController::class.':view')->setName('user_view');
        $app->post('/{id}/delete', UserController::class.':delete')->setName('user_delete');
    })->add($container['security.authentication.middleware']);
});
