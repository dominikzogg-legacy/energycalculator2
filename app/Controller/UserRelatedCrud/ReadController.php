<?php

declare(strict_types=1);

namespace Energycalculator\Controller\UserRelatedCrud;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Model\Traits\OwnedByUserTrait;
use Energycalculator\Service\TwigRender;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ReadController
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @param string                  $type
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface  $authorization
     * @param ErrorResponseHandler    $errorResponseHandler
     * @param RepositoryInterface     $repository
     * @param TwigRender              $twig
     */
    public function __construct(
        string $type,
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        ErrorResponseHandler $errorResponseHandler,
        RepositoryInterface $repository,
        TwigRender $twig
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->errorResponseHandler = $errorResponseHandler;
        $this->repository = $repository;
        $this->type = $type;
        $this->twig = $twig;
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

        $id = $request->getAttribute('id');

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        /** @var OwnedByUserTrait|ModelInterface $element */
        $element = $this->repository->findOneBy(['id' => $id, 'userId' => $authenticatedUser->getId()]);
        if (null === $element) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                404,
                sprintf('%s.error.notfound', $typeLower)
            );
        }

        if (!$this->authorization->isGranted($authenticatedUser, sprintf('%s_READ', $typeUpper), $element)) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                sprintf('%s.error.permissiondenied', $typeLower)
            );
        }

        return $this->twig->render($response, sprintf('@Energycalculator/%s/read.html.twig', $typeLower),
            $this->twig->aggregate($request, [
                'element' => prepareForView($element),
            ])
        );
    }
}
