<?php

declare(strict_types=1);

namespace Energycalculator\ServiceProvider;

use Chubbyphp\Security\Authentication\FormAuthentication;
use Energycalculator\Collection\ComestibleCollection;
use Energycalculator\Collection\DayCollection;
use Energycalculator\Collection\UserCollection;
use Energycalculator\Controller\AuthController;
use Energycalculator\Controller\ChartController;
use Energycalculator\Controller\ComestibleController;
use Energycalculator\Controller\HomeController;
use Energycalculator\Controller\UserController;
use Energycalculator\Controller\UserRelatedCrud\CreateController;
use Energycalculator\Controller\UserRelatedCrud\DeleteController;
use Energycalculator\Controller\UserRelatedCrud\ListController;
use Energycalculator\Controller\UserRelatedCrud\ReadController;
use Energycalculator\Controller\UserRelatedCrud\UpdateController;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Model\Comestible;
use Energycalculator\Model\Day;
use Energycalculator\Model\User;
use Energycalculator\Repository\Repository;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TwigRender;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Energycalculator\Factory\Model\DayFactory;
use Energycalculator\Factory\Model\ComestibleFactory;

final class ControllerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['comestible.controller.list'] = function () use ($container) {
            return new ListController(
                'comestible',
                ComestibleCollection::class,
                $container['security.authentication'],
                $container['security.authorization'],
                $container['deserializer'],
                $container[ErrorResponseHandler::class],
                $container[Repository::class.Comestible::class],
                $container['session'],
                $container[TwigRender::class],
                $container['validator']
            );
        };
        $container['comestible.controller.create'] = function () use ($container) {
            return new CreateController(
                'comestible',
                $container['security.authentication'],
                $container['security.authorization'],
                $container['deserializer'],
                $container[ErrorResponseHandler::class],
                $container[ComestibleFactory::class],
                $container[RedirectForPath::class],
                $container[Repository::class.Comestible::class],
                $container['session'],
                $container[TwigRender::class],
                $container['validator']
            );
        };
        $container['comestible.controller.read'] = function () use ($container) {
            return new ReadController(
                'comestible',
                $container['security.authentication'],
                $container['security.authorization'],
                $container[ErrorResponseHandler::class],
                $container[Repository::class.Comestible::class],
                $container[TwigRender::class]
            );
        };
        $container['comestible.controller.update'] = function () use ($container) {
            return new UpdateController(
                'comestible',
                $container['security.authentication'],
                $container['security.authorization'],
                $container['deserializer'],
                $container[ErrorResponseHandler::class],
                $container[RedirectForPath::class],
                $container[Repository::class.Comestible::class],
                $container['session'],
                $container[TwigRender::class],
                $container['validator']
            );
        };
        $container['comestible.controller.delete'] = function () use ($container) {
            return new DeleteController(
                'comestible',
                $container['security.authentication'],
                $container['security.authorization'],
                $container[ErrorResponseHandler::class],
                $container[RedirectForPath::class],
                $container[Repository::class.Comestible::class]
            );
        };
        $container['day.controller.list'] = function () use ($container) {
            return new ListController(
                'day',
                DayCollection::class,
                $container['security.authentication'],
                $container['security.authorization'],
                $container['deserializer'],
                $container[ErrorResponseHandler::class],
                $container[Repository::class.Day::class],
                $container['session'],
                $container[TwigRender::class],
                $container['validator']
            );
        };
        $container['day.controller.create'] = function () use ($container) {
            return new CreateController(
                'day',
                $container['security.authentication'],
                $container['security.authorization'],
                $container['deserializer'],
                $container[ErrorResponseHandler::class],
                $container[DayFactory::class],
                $container[RedirectForPath::class],
                $container[Repository::class.Day::class],
                $container['session'],
                $container[TwigRender::class],
                $container['validator']
            );
        };
        $container['day.controller.read'] = function () use ($container) {
            return new ReadController(
                'day',
                $container['security.authentication'],
                $container['security.authorization'],
                $container[ErrorResponseHandler::class],
                $container[Repository::class.Day::class],
                $container[TwigRender::class]
            );
        };
        $container['day.controller.update'] = function () use ($container) {
            return new UpdateController(
                'day',
                $container['security.authentication'],
                $container['security.authorization'],
                $container['deserializer'],
                $container[ErrorResponseHandler::class],
                $container[RedirectForPath::class],
                $container[Repository::class.Day::class],
                $container['session'],
                $container[TwigRender::class],
                $container['validator']
            );
        };
        $container['day.controller.delete'] = function () use ($container) {
            return new DeleteController(
                'day',
                $container['security.authentication'],
                $container['security.authorization'],
                $container[ErrorResponseHandler::class],
                $container[RedirectForPath::class],
                $container[Repository::class.Day::class]
            );
        };
        $container[AuthController::class] = function () use ($container) {
            return new AuthController(
                $container[FormAuthentication::class], // need cause login/logout
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
                $container[Repository::class.Comestible::class],
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
                $container[Repository::class.User::class],
                $container['validator']
            );
        };
        $container[ChartController::class] = function () use ($container) {
            return new ChartController(
                $container['security.authentication'],
                $container['security.authorization'],
                $container[Repository::class.Day::class],
                $container['deserializer'],
                $container[ErrorResponseHandler::class],
                $container[TwigRender::class]
            );
        };
    }
}
