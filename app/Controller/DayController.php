<?php

namespace Energycalculator\Controller;

use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Energycalculator\Model\Day;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;

final class DayController
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
     * @var RepositoryInterface
     */
    private $dayRepository;

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
     * @param AuthorizationInterface $authorization
     * @param RepositoryInterface $dayRepository
     * @param RedirectForPath $redirectForPath
     * @param SessionInterface $session
     * @param TemplateData $templateData
     * @param TwigRender $twig
     * @param ValidatorInterface $validator
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        RepositoryInterface $dayRepository,
        RedirectForPath $redirectForPath,
        SessionInterface $session,
        TemplateData $templateData,
        TwigRender $twig,
        ValidatorInterface $validator
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->dayRepository = $dayRepository;
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
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        $days = $this->dayRepository->findBy(['userId' => $authenticatedUser->getId()]);

        return $this->twig->render($response, '@Energycalculator/day/list.html.twig',
            $this->templateData->aggregate($request, [
                'days' => prepareForView($days),
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

        /** @var Day $day */
        $day = $this->dayRepository->find($id);
        if (null === $day) {
            throw HttpException::create($request, $response, 404, 'day.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_VIEW', $day)) {
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        return $this->twig->render($response, '@Energycalculator/day/view.html.twig',
            $this->templateData->aggregate($request, [
                'day' => prepareForView($day),
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
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        $day = new Day();
        $day = $day->withDate(new \DateTime());

        if ('POST' === $request->getMethod()) {
            $data = $request->getParsedBody();

            $day = $day
                ->withUser($authenticatedUser)
                ->withDate(new \DateTime($data['date'] ?? 'now'))
                ->withWeight($data['weight'] ? (float) $data['weight'] : null)
            ;

            if ([] === $errorMessages = $this->validator->validateModel($day)) {
                $this->dayRepository->insert($day);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'day.flash.create.success')
                );

                return $this->redirectForPath->get($response, 302, 'day_edit', [
                    'locale' => $request->getAttribute('locale'),
                    'id' => $day->getId(),
                ]);
            }

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'day.flash.create.failed')
            );
        }

        return $this->twig->render($response, '@Energycalculator/day/create.html.twig',
            $this->templateData->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'day' => prepareForView($day),
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

        /** @var Day $day */
        $day = $this->dayRepository->find($id);
        if (null === $day) {
            throw HttpException::create($request, $response, 404, 'day.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_EDIT', $day)) {
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        if ('POST' === $request->getMethod()) {
            $data = $request->getParsedBody();

            $day = $day
                ->withDate(new \DateTime($data['date'] ?? 'now'))
                ->withWeight($data['weight'] ? (float) $data['weight'] : null)
            ;

            if ([] === $errorMessages = $this->validator->validateModel($day)) {
                $day = $day->withUpdatedAt(new \DateTime());

                $this->dayRepository->update($day);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'day.flash.edit.success')
                );

                return $this->redirectForPath->get($response, 302, 'day_edit', [
                    'locale' => $request->getAttribute('locale'),
                    'id' => $day->getId(),
                ]);
            }

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'day.flash.edit.failed')
            );
        }

        return $this->twig->render($response, '@Energycalculator/day/edit.html.twig',
            $this->templateData->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'day' => prepareForView($day)
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

        /** @var Day $day */
        $day = $this->dayRepository->find($id);
        if (null === $day) {
            throw HttpException::create($request, $response, 404, 'day.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_DELETE', $day)) {
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        $this->dayRepository->delete($day);

        return $this->redirectForPath->get(
            $response, 302, 'day_list', ['locale' => $request->getAttribute('locale')]
        );
    }
}