<?php

declare(strict_types=1);

namespace Energycalculator\Controller\UserRelatedCrud;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Repository\RepositoryInterface;
use Energycalculator\Service\TwigRender;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ListController
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $collectionClass;

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
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param string                  $type
     * @param string                  $collectionClass
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface  $authorization
     * @param DeserializerInterface   $deserializer
     * @param ErrorResponseHandler    $errorResponseHandler
     * @param RepositoryInterface     $repository
     * @param SessionInterface        $session
     * @param TwigRender              $twig
     * @param ValidatorInterface      $validator
     */
    public function __construct(
        string $type,
        string $collectionClass,
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        DeserializerInterface $deserializer,
        ErrorResponseHandler $errorResponseHandler,
        RepositoryInterface $repository,
        SessionInterface $session,
        TwigRender $twig,
        ValidatorInterface $validator
    ) {
        $this->type = $type;
        $this->collectionClass = $collectionClass;
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->deserializer = $deserializer;
        $this->errorResponseHandler = $errorResponseHandler;
        $this->repository = $repository;
        $this->session = $session;
        $this->twig = $twig;
        $this->validator = $validator;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $typeLower = strtolower($this->type);
        $typeUpper = strtoupper($this->type);

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, sprintf('%s_LIST', $typeUpper))) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                sprintf('%s.error.permissiondenied', $typeLower)
            );
        }

        $collection = $this->deserializer->denormalize($this->collectionClass, $request->getQueryParams());
        $collection->setUser($authenticatedUser);

        $errorMessages = [];
        if ([] !== $errors = $this->validator->validate($collection)) {
            $errorMessages = $this->twig->getErrorMessages($request->getAttribute('locale'), $errors);

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, sprintf('%s.flash.list.failed', $typeLower))
            );
        } else {
            $this->repository->resolveCollection($collection);
        }

        return $this->twig->render($response, sprintf('@Energycalculator/%s/list.html.twig', $typeLower),
            $this->twig->aggregate($request, array_replace_recursive(
                \Energycalculator\prepareForView($collection),
                ['errorMessages' => $errorMessages]
            ))
        );
    }
}
