<?php

namespace Energycalculator\Controller;

use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Repository\ComestibleRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface $authorization
     * @param ComestibleRepository $comestibleRepository
     * @param ErrorResponseHandler $errorResponseHandler
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        ComestibleRepository $comestibleRepository,
        ErrorResponseHandler $errorResponseHandler
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->comestibleRepository = $comestibleRepository;
        $this->errorResponseHandler = $errorResponseHandler;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function findByNameLike(Request $request, Response $response)
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'COMESTIBLE_LIST')) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                'comestible.error.permissiondenied'
            );
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
