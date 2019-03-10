<?php

declare(strict_types=1);

namespace Energycalculator\Controller;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Security\Authorization\RoleHierarchyResolverInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Repository\UserRepository;
use Energycalculator\Collection\UserCollection;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Energycalculator\Model\User;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TwigRender;
use Energycalculator\Repository\RepositoryInterface;

final class UserController
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var DeserializerInterface
     */
    private $deserializer;

    /**
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @var RedirectForPath
     */
    private $redirectForPath;

    /**
     * @var RoleHierarchyResolverInterface
     */
    private $roleHierarchyResolver;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param AuthenticationInterface        $authentication
     * @param AuthorizationInterface         $authorization
     * @param DeserializerInterface          $deserializer
     * @param ErrorResponseHandler           $errorResponseHandler
     * @param RedirectForPath                $redirectForPath
     * @param RoleHierarchyResolverInterface $roleHierarchyResolver
     * @param SessionInterface               $session
     * @param TwigRender                     $twig
     * @param RepositoryInterface            $userRepository
     * @param ValidatorInterface             $validator
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        DeserializerInterface $deserializer,
        ErrorResponseHandler $errorResponseHandler,
        RedirectForPath $redirectForPath,
        RoleHierarchyResolverInterface $roleHierarchyResolver,
        SessionInterface $session,
        TwigRender $twig,
        RepositoryInterface $userRepository,
        ValidatorInterface $validator
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->deserializer = $deserializer;
        $this->errorResponseHandler = $errorResponseHandler;
        $this->redirectForPath = $redirectForPath;
        $this->roleHierarchyResolver = $roleHierarchyResolver;
        $this->session = $session;
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function listAll(Request $request, Response $response)
    {
        if (!$this->authorization->isGranted($this->authentication->getAuthenticatedUser($request), 'ADMIN')) {
            return $this->errorResponseHandler->errorReponse($request, $response, 403, 'user.error.permissiondenied');
        }

        /** @var UserCollection $collection */
        $collection = $this->deserializer->denormalize(UserCollection::class, $request->getQueryParams());

        $errorMessages = [];
        if ([] !== $errors = $this->validator->validate($collection)) {
            $errorMessages = $this->twig->getErrorMessages($request->getAttribute('locale'), $errors);

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'user.flash.list.failed')
            );
        } else {
            $this->userRepository->resolveCollection($collection);
        }

        return $this->twig->render($response, '@Energycalculator/user/list.html.twig',
            $this->twig->aggregate($request, array_replace_recursive(
                \Energycalculator\prepareForView($collection),
                ['errorMessages' => $errorMessages]
            ))
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function read(Request $request, Response $response)
    {
        if (!$this->authorization->isGranted($this->authentication->getAuthenticatedUser($request), 'ADMIN')) {
            return $this->errorResponseHandler->errorReponse($request, $response, 403, 'user.error.permissiondenied');
        }

        $id = $request->getAttribute('id');

        $user = $this->userRepository->find($id);
        if (null === $user) {
            return $this->errorResponseHandler->errorReponse($request, $response, 404, 'user.error.notfound');
        }

        return $this->twig->render($response, '@Energycalculator/user/read.html.twig',
            $this->twig->aggregate($request, [
                'user' => \Energycalculator\prepareForView($user),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'ADMIN')) {
            return $this->errorResponseHandler->errorReponse($request, $response, 403, 'user.error.permissiondenied');
        }

        $user = new User();

        if ('POST' === $request->getMethod()) {
            /** @var User $user */
            $user = $this->deserializer->denormalize($user, $request->getParsedBody());

            $locale = $request->getAttribute('locale');

            if ([] === $errors = $this->validator->validate($user)) {
                $this->userRepository->persist($user);
                $this->userRepository->flush();

                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'user.flash.create.success')
                );

                return $this->redirectForPath->get($response, 302, 'user_update', [
                    'locale' => $locale,
                    'id' => $user->getId(),
                ]);
            }

            $errorMessages = $this->twig->getErrorMessages($locale, $errors);

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'user.flash.create.failed')
            );
        }

        $possibleRoles = $this->roleHierarchyResolver->resolve(['ADMIN']);

        return $this->twig->render($response, '@Energycalculator/user/create.html.twig',
            $this->twig->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'user' => \Energycalculator\prepareForView($user),
                'possibleRoles' => array_combine($possibleRoles, $possibleRoles),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function update(Request $request, Response $response)
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'ADMIN')) {
            return $this->errorResponseHandler->errorReponse($request, $response, 403, 'user.error.permissiondenied');
        }

        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            return $this->errorResponseHandler->errorReponse($request, $response, 404, 'user.error.notfound');
        }

        if ('POST' === $request->getMethod()) {
            /** @var User $user */
            $user = $this->deserializer->denormalize($user, $request->getParsedBody());

            $locale = $request->getAttribute('locale');

            if ([] === $errors = $this->validator->validate($user)) {
                $this->userRepository->persist($user);
                $this->userRepository->flush();

                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'user.flash.update.success')
                );

                return $this->redirectForPath->get($response, 302, 'user_update', [
                    'locale' => $locale,
                    'id' => $user->getId(),
                ]);
            }

            $errorMessages = $this->twig->getErrorMessages($locale, $errors);

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'user.flash.update.failed')
            );
        }

        $possibleRoles = $this->roleHierarchyResolver->resolve(['ADMIN']);

        return $this->twig->render($response, '@Energycalculator/user/update.html.twig',
            $this->twig->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'user' => \Energycalculator\prepareForView($user),
                'possibleRoles' => array_combine($possibleRoles, $possibleRoles),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'ADMIN')) {
            return $this->errorResponseHandler->errorReponse($request, $response, 403, 'user.error.permissiondenied');
        }

        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            return $this->errorResponseHandler->errorReponse($request, $response, 404, 'user.error.notfound');
        }

        if ($authenticatedUser->getId() === $user->getId()) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                'user.error.cantdeleteyourself'
            );
        }

        $this->userRepository->remove($user);
        $this->userRepository->flush();

        return $this->redirectForPath->get($response, 302, 'user_list', ['locale' => $request->getAttribute('locale')]);
    }
}
