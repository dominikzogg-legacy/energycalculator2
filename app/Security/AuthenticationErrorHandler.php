<?php

namespace Energycalculator\Security;

use Chubbyphp\Security\Authentication\AuthenticationErrorHandlerInterface;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthenticationErrorHandler implements AuthenticationErrorHandlerInterface
{
    /**
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @param Request $request
     * @param Response $response
     * @param int $code
     * @return Response
     */
    public function errorResponse(Request $request, Response $response, int $code): Response
    {
        return $this->errorResponseHandler->errorReponse($request, $response, $code);
    }
}
