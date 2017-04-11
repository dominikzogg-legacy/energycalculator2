<?php

use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Validation\Validator;
use Energycalculator\Controller\AuthController;
use Energycalculator\Controller\ComestibleController;
use Energycalculator\Controller\DayController;
use Energycalculator\Controller\HomeController;
use Energycalculator\Controller\UserController;
use Energycalculator\Repository\ComestibleRepository;
use Energycalculator\Repository\ComestibleWithinDayRepository;
use Energycalculator\Repository\DayRepository;
use Energycalculator\Repository\UserRepository;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;
use Slim\App;
use Slim\Container;

/* @var App $app */
/* @var Container $container */

$container[AuthController::class] = function () use ($container) {
    return new AuthController(
        $container[FormAuthentication::class], // need cause login/logout
        $container[RedirectForPath::class],
        $container['session']
    );
};

$container[HomeController::class] = function () use ($container) {
    return new HomeController($container[TemplateData::class], $container[TwigRender::class]);
};

$container[ComestibleController::class] = function () use ($container) {
    return new ComestibleController(
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ComestibleRepository::class],
        $container[Deserializer::class],
        $container[RedirectForPath::class],
        $container['session'],
        $container[TemplateData::class],
        $container[TwigRender::class],
        $container[Validator::class]
    );
};

$container[DayController::class] = function () use ($container) {
    return new DayController(
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ComestibleRepository::class],
        $container[ComestibleWithinDayRepository::class],
        $container[DayRepository::class],
        $container[Deserializer::class],
        $container[RedirectForPath::class],
        $container['session'],
        $container[TemplateData::class],
        $container[TwigRender::class],
        $container[Validator::class]
    );
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container['security.authentication'],
        $container['security.authorization'],
        $container[Deserializer::class],
        $container[RedirectForPath::class],
        $container['security.authorization.rolehierarchyresolver'],
        $container['session'],
        $container[TemplateData::class],
        $container[TwigRender::class],
        $container[UserRepository::class],
        $container[Validator::class]
    );
};

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
        $app->get('/findbynamelike', ComestibleController::class.':findByNameLike')->setName('comestible_findbynamelike');
    })->add($container['security.authentication.middleware']);

    $app->group('/days', function () use ($app, $container) {
        $app->get('', DayController::class.':listAll')->setName('day_list');
        $app->map(['GET', 'POST'], '/create', DayController::class.':create')->setName('day_create');
        $app->map(['GET', 'POST'], '/{id}/edit', DayController::class.':edit')->setName('day_edit');
        $app->get('/{id}/view', DayController::class.':view')->setName('day_view');
        $app->post('/{id}/delete', DayController::class.':delete')->setName('day_delete');
    })->add($container['security.authentication.middleware']);

    $app->group('/users', function () use ($app, $container) {
        $app->get('', UserController::class.':listAll')->setName('user_list');
        $app->map(['GET', 'POST'], '/create', UserController::class.':create')->setName('user_create');
        $app->map(['GET', 'POST'], '/{id}/edit', UserController::class.':edit')->setName('user_edit');
        $app->get('/{id}/view', UserController::class.':view')->setName('user_view');
        $app->post('/{id}/delete', UserController::class.':delete')->setName('user_delete');
    })->add($container['security.authentication.middleware']);
});
