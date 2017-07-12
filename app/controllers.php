<?php

use Chubbyphp\Security\Authentication\FormAuthentication;
use Energycalculator\Controller\UserRelatedCrud\CreateController;
use Energycalculator\Controller\UserRelatedCrud\DeleteController;
use Energycalculator\Controller\UserRelatedCrud\ListController;
use Energycalculator\Controller\UserRelatedCrud\ReadController;
use Energycalculator\Controller\UserRelatedCrud\UpdateController;
use Energycalculator\Controller\AuthController;
use Energycalculator\Controller\ComestibleController;
use Energycalculator\Controller\HomeController;
use Energycalculator\Controller\UserController;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\Day;
use Energycalculator\Repository\ComestibleRepository;
use Energycalculator\Repository\DayRepository;
use Energycalculator\Repository\UserRepository;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TwigRender;
use Slim\App;
use Slim\Container;

/* @var App $app */
/* @var Container $container */

$container['comestible.constroller.list'] = function () use ($container) {
    return new ListController(
        'comestible',
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ErrorResponseHandler::class],
        $container[ComestibleRepository::class],
        $container[TwigRender::class]
    );
};

$container['comestible.constroller.create'] = function () use ($container) {
    return new CreateController(
        'comestible',
        $container['security.authentication'],
        $container['security.authorization'],
        $container['deserializer'],
        $container[ErrorResponseHandler::class],
        [Comestible::class, 'create'],
        $container[RedirectForPath::class],
        $container[ComestibleRepository::class],
        $container['session'],
        $container[TwigRender::class],
        $container['validator']
    );
};

$container['comestible.constroller.read'] = function () use ($container) {
    return new ReadController(
        'comestible',
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ErrorResponseHandler::class],
        $container[ComestibleRepository::class],
        $container[TwigRender::class]
    );
};

$container['comestible.constroller.update'] = function () use ($container) {
    return new UpdateController(
        'comestible',
        $container['security.authentication'],
        $container['security.authorization'],
        $container['deserializer'],
        $container[ErrorResponseHandler::class],
        $container[RedirectForPath::class],
        $container[ComestibleRepository::class],
        $container['session'],
        $container[TwigRender::class],
        $container['validator']
    );
};

$container['comestible.constroller.delete'] = function () use ($container) {
    return new DeleteController(
        'comestible',
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ErrorResponseHandler::class],
        $container[RedirectForPath::class],
        $container[ComestibleRepository::class]
    );
};

$container['day.constroller.list'] = function () use ($container) {
    return new ListController(
        'day',
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ErrorResponseHandler::class],
        $container[DayRepository::class],
        $container[TwigRender::class]
    );
};

$container['day.constroller.create'] = function () use ($container) {
    return new CreateController(
        'day',
        $container['security.authentication'],
        $container['security.authorization'],
        $container['deserializer'],
        $container[ErrorResponseHandler::class],
        [Day::class, 'create'],
        $container[RedirectForPath::class],
        $container[DayRepository::class],
        $container['session'],
        $container[TwigRender::class],
        $container['validator']
    );
};

$container['day.constroller.read'] = function () use ($container) {
    return new ReadController(
        'day',
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ErrorResponseHandler::class],
        $container[DayRepository::class],
        $container[TwigRender::class]
    );
};

$container['day.constroller.update'] = function () use ($container) {
    return new UpdateController(
        'day',
        $container['security.authentication'],
        $container['security.authorization'],
        $container['deserializer'],
        $container[ErrorResponseHandler::class],
        $container[RedirectForPath::class],
        $container[DayRepository::class],
        $container['session'],
        $container[TwigRender::class],
        $container['validator']
    );
};

$container['day.constroller.delete'] = function () use ($container) {
    return new DeleteController(
        'day',
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ErrorResponseHandler::class],
        $container[RedirectForPath::class],
        $container[DayRepository::class]
    );
};

$container[AuthController::class] = function () use ($container) {
    return new AuthController(
        $container[FormAuthentication::class], // need cause login/logout
        $container[RedirectForPath::class],
        $container['session']
    );
};

$container[HomeController::class] = function () use ($container) {
    return new HomeController($container[TwigRender::class]);
};

$container[ComestibleController::class] = function () use ($container) {
    return new ComestibleController(
        $container['security.authentication'],
        $container['security.authorization'],
        $container[ComestibleRepository::class],
        $container[ErrorResponseHandler::class]
    );
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container['security.authentication'],
        $container['security.authorization'],
        $container['deserializer'],
        $container[ErrorResponseHandler::class],
        $container[RedirectForPath::class],
        $container['security.authorization.rolehierarchyresolver'],
        $container['session'],
        $container[TwigRender::class],
        $container[UserRepository::class],
        $container['validator']
    );
};

$app->group('/{locale:'.implode('|', $container['locales']).'}', function () use ($app, $container) {
    $app->get('', HomeController::class.':home')->setName('home');

    $app->post('/login', AuthController::class.':login')->setName('login');
    $app->post('/logout', AuthController::class.':logout')->setName('logout');

    $app->group('/comestibles', function () use ($app, $container) {
        $app->get('', 'comestible.constroller.list')->setName('comestible_list');
        $app->map(['GET', 'POST'], '/create', 'comestible.constroller.create')->setName('comestible_create');
        $app->get('/{id}/read', 'comestible.constroller.read')->setName('comestible_read');
        $app->map(['GET', 'POST'], '/{id}/update', 'comestible.constroller.update')->setName('comestible_update');
        $app->post('/{id}/delete', 'comestible.constroller.delete')->setName('comestible_delete');
        $app->get('/findbynamelike', ComestibleController::class.':findByNameLike')->setName('comestible_findbynamelike');
    })->add($container['security.authentication.middleware']);

    $app->group('/days', function () use ($app, $container) {
        $app->get('', 'day.constroller.list')->setName('day_list');
        $app->map(['GET', 'POST'], '/create', 'day.constroller.create')->setName('day_create');
        $app->get('/{id}/read', 'day.constroller.read')->setName('day_read');
        $app->map(['GET', 'POST'], '/{id}/update', 'day.constroller.update')->setName('day_update');
        $app->post('/{id}/delete', 'day.constroller.delete')->setName('day_delete');
    })->add($container['security.authentication.middleware']);

    $app->group('/users', function () use ($app, $container) {
        $app->get('', UserController::class.':listAll')->setName('user_list');
        $app->map(['GET', 'POST'], '/create', UserController::class.':create')->setName('user_create');
        $app->get('/{id}/read', UserController::class.':read')->setName('user_read');
        $app->map(['GET', 'POST'], '/{id}/update', UserController::class.':update')->setName('user_update');
        $app->post('/{id}/delete', UserController::class.':delete')->setName('user_delete');
    })->add($container['security.authentication.middleware']);
});
