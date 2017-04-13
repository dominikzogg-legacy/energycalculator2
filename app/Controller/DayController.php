<?php

namespace Energycalculator\Controller;

use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Energycalculator\Model\Day;
use Energycalculator\Repository\ComestibleRepository;
use Energycalculator\Repository\ComestibleWithinDayRepository;
use Energycalculator\Repository\DayRepository;
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
     * @var ComestibleRepository
     */
    private $comestibleRepository;

    /**
     * @var ComestibleWithinDayRepository
     */
    private $comestibleWithinDayRepository;

    /**
     * @var DayRepository
     */
    private $dayRepository;

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
     * @param AuthenticationInterface       $authentication
     * @param AuthorizationInterface        $authorization
     * @param ComestibleRepository          $comestibleRepository
     * @param ComestibleWithinDayRepository $comestibleWithinDayRepository
     * @param DayRepository                 $dayRepository
     * @param DeserializerInterface         $deserializer
     * @param RedirectForPath               $redirectForPath
     * @param SessionInterface              $session
     * @param TemplateData                  $templateData
     * @param TwigRender                    $twig
     * @param ValidatorInterface            $validator
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        ComestibleRepository $comestibleRepository,
        ComestibleWithinDayRepository $comestibleWithinDayRepository,
        DayRepository $dayRepository,
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
        $this->comestibleWithinDayRepository = $comestibleWithinDayRepository;
        $this->dayRepository = $dayRepository;
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

        if (!$this->authorization->isGranted($authenticatedUser, 'DAY_LIST')) {
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        $days = $this->dayRepository->findBy(['userId' => $authenticatedUser->getId()], ['date' => 'DESC']);

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
        if (!$this->authorization->isGranted($authenticatedUser, 'DAY_VIEW', $day)) {
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

        if (!$this->authorization->isGranted($authenticatedUser, 'DAY_CREATE')) {
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        $day = Day::create();
        $day->setUser($authenticatedUser);

        if ('POST' === $request->getMethod()) {
            /** @var Day|ModelInterface $day */
            $day = $this->deserializer->deserializeByObject($request->getParsedBody(), $day);

            $locale = $request->getAttribute('locale');

            if ([] === $errors = $this->validator->validateObject($day)) {
                $this->dayRepository->persist($day);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'day.flash.create.success')
                );

                return $this->redirectForPath->get($response, 302, 'day_edit', [
                    'locale' => $locale,
                    'id' => $day->getId(),
                ]);
            }

            $errorMessages = $this->templateData->getErrorMessages($locale, $errors);

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

        /** @var Day|ModelInterface $day */
        $day = $this->dayRepository->find($id);
        if (null === $day) {
            throw HttpException::create($request, $response, 404, 'day.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'DAY_EDIT', $day)) {
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        if ('POST' === $request->getMethod()) {
            /** @var Day|ModelInterface $day */
            $day = $this->deserializer->deserializeByObject($request->getParsedBody(), $day);

            $locale = $request->getAttribute('locale');

            if ([] === $errors = $this->validator->validateObject($day)) {
                $this->dayRepository->persist($day);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'day.flash.edit.success')
                );

                return $this->redirectForPath->get($response, 302, 'day_edit', [
                    'locale' => $locale,
                    'id' => $day->getId(),
                ]);
            }

            $errorMessages = $this->templateData->getErrorMessages($locale, $errors);

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'day.flash.edit.failed')
            );
        }

        return $this->twig->render($response, '@Energycalculator/day/edit.html.twig',
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
    public function delete(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var Day|ModelInterface $day */
        $day = $this->dayRepository->find($id);
        if (null === $day) {
            throw HttpException::create($request, $response, 404, 'day.error.notfound');
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, 'DAY_DELETE', $day)) {
            throw HttpException::create($request, $response, 403, 'day.error.permissiondenied');
        }

        $this->dayRepository->remove($day);

        return $this->redirectForPath->get(
            $response, 302, 'day_list', ['locale' => $request->getAttribute('locale')]
        );
    }
}
