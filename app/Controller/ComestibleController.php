<?php

namespace Energycalculator\Controller;

use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Energycalculator\Model\Comestible;
use Energycalculator\Repository\ComestibleRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;

final class ComestibleController
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
     * @var ComestibleRepository
     */
    private $comestibleRepository;

    /**
     * @var DeserializerInterface
     */
    private $deserializer;

    /**
     * @var RedirectForPath
     */
    private $redirectForPath;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface  $authorization
     * @param ComestibleRepository    $comestibleRepository
     * @param DeserializerInterface   $deserializer
     * @param RedirectForPath         $redirectForPath
     * @param SessionInterface        $session
     * @param TemplateData            $templateData
     * @param TwigRender              $twig
     * @param ValidatorInterface      $validator
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        ComestibleRepository $comestibleRepository,
        DeserializerInterface $deserializer,
        RedirectForPath $redirectForPath,
        SessionInterface $session,
        TemplateData $templateData,
        TwigRender $twig,
        ValidatorInterface $validator
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->comestibleRepository = $comestibleRepository;
        $this->deserializer = $deserializer;
        $this->redirectForPath = $redirectForPath;
        $this->session = $session;
        $this->templateData = $templateData;
        $this->twig = $twig;
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
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_LIST')) {
            throw HttpException::create($request, $response, 403, 'comestible.error.permissiondenied');
        }

        $comestibles = $this->comestibleRepository->findBy(['userId' => $authenticatedUser->getId()]);

        return $this->twig->render($response, '@Energycalculator/comestible/list.html.twig',
            $this->templateData->aggregate($request, [
                'comestibles' => prepareForView($comestibles),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function view(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var Comestible|ModelInterface $comestible */
        $comestible = $this->comestibleRepository->find($id);
        if (null === $comestible) {
            throw HttpException::create($request, $response, 404, 'comestible.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_VIEW', $comestible)) {
            throw HttpException::create($request, $response, 403, 'comestible.error.permissiondenied');
        }

        return $this->twig->render($response, '@Energycalculator/comestible/view.html.twig',
            $this->templateData->aggregate($request, [
                'comestible' => prepareForView($comestible),
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

        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_CREATE')) {
            throw HttpException::create($request, $response, 403, 'comestible.error.permissiondenied');
        }

        $comestible = Comestible::create();
        $comestible->setUser($authenticatedUser);

        if ('POST' === $request->getMethod()) {
            /** @var Comestible|ModelInterface $comestible */
            $comestible = $this->deserializer->deserializeByObject($request->getParsedBody(), $comestible);

            if ([] === $errorMessages = $this->validator->validateObject($comestible)) {
                $this->comestibleRepository->persist($comestible);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'comestible.flash.create.success')
                );

                return $this->redirectForPath->get($response, 302, 'comestible_edit', [
                    'locale' => $request->getAttribute('locale'),
                    'id' => $comestible->getId(),
                ]);
            }

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'comestible.flash.create.failed')
            );
        }

        return $this->twig->render($response, '@Energycalculator/comestible/create.html.twig',
            $this->templateData->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'comestible' => prepareForView($comestible),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function edit(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var Comestible|ModelInterface $comestible */
        $comestible = $this->comestibleRepository->find($id);
        if (null === $comestible) {
            throw HttpException::create($request, $response, 404, 'comestible.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_EDIT', $comestible)) {
            throw HttpException::create($request, $response, 403, 'comestible.error.permissiondenied');
        }

        if ('POST' === $request->getMethod()) {
            /** @var Comestible|ModelInterface $comestible */
            $comestible = $this->deserializer->deserializeByObject($request->getParsedBody(), $comestible);

            if ([] === $errorMessages = $this->validator->validateObject($comestible)) {
                $this->comestibleRepository->persist($comestible);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'comestible.flash.edit.success')
                );

                return $this->redirectForPath->get($response, 302, 'comestible_edit', [
                    'locale' => $request->getAttribute('locale'),
                    'id' => $comestible->getId(),
                ]);
            }

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'comestible.flash.edit.failed')
            );
        }

        return $this->twig->render($response, '@Energycalculator/comestible/edit.html.twig',
            $this->templateData->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'comestible' => prepareForView($comestible),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function delete(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var Comestible|ModelInterface $comestible */
        $comestible = $this->comestibleRepository->find($id);
        if (null === $comestible) {
            throw HttpException::create($request, $response, 404, 'comestible.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_DELETE', $comestible)) {
            throw HttpException::create($request, $response, 403, 'comestible.error.permissiondenied');
        }

        $this->comestibleRepository->remove($comestible);

        return $this->redirectForPath->get(
            $response, 302, 'comestible_list', ['locale' => $request->getAttribute('locale')]
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function findByNameLike(Request $request, Response $response)
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_LIST')) {
            throw HttpException::create($request, $response, 403, 'comestible.error.permissiondenied');
        }

        $queryParams = $request->getQueryParams();

        $rows = $this->comestibleRepository->findRowsByNameLike(
            $authenticatedUser->getId(),
            $queryParams['q'] ?? ''
        );

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($rows));

        return $response;
    }
}
