<?php

declare(strict_types=1);

namespace Energycalculator\Middleware;

use Chubbyphp\Csrf\CsrfTokenGeneratorInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class CsrfMiddleware
{
    /**
     * @var CsrfTokenGeneratorInterface
     */
    private $csrfTokenGenerator;

    /**
     * @var SessionInterface
     */
    private $session;

    const CSRF_KEY = 'csrf';

    /**
     * @var ErrorResponseHandler
     */
    private $responseHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    const EXCEPTION_STATUS = 424;

    const EXCEPTION_MISSING_IN_SESSION = 'Csrf token is missing within session';
    const EXCEPTION_MISSING_IN_BODY = 'Csrf token is missing within body';
    const EXCEPTION_IS_NOT_SAME = 'Csrf token within body is not the same as in session';

    /**
     * @param CsrfTokenGeneratorInterface $csrfTokenGenerator
     * @param SessionInterface            $session
     * @param LoggerInterface|null        $logger
     */
    public function __construct(
        CsrfTokenGeneratorInterface $csrfTokenGenerator,
        SessionInterface $session,
        ErrorResponseHandler $responseHandler,
        LoggerInterface $logger = null
    ) {
        $this->csrfTokenGenerator = $csrfTokenGenerator;
        $this->session = $session;
        $this->responseHandler = $responseHandler;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $this->logger->info('csrf: check token');
            if (null !== $errorResponse = $this->checkCsrf($request, $response)) {
                return $errorResponse;
            }
        }

        if (!$this->session->has($request, self::CSRF_KEY)) {
            $this->logger->info('csrf: set token');
            $this->session->set($request, self::CSRF_KEY, $this->csrfTokenGenerator->generate());
        }

        if (null !== $next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return Response|null
     */
    private function checkCsrf(Request $request, Response $response)
    {
        if ($this->session->has($request, self::CSRF_KEY)) {
            return $this->errorResponse($request, $response, self::EXCEPTION_MISSING_IN_SESSION);
        }

        $data = $request->getParsedBody();

        if (!isset($data[self::CSRF_KEY])) {
            return $this->errorResponse($request, $response, self::EXCEPTION_MISSING_IN_BODY);
        }

        if ($this->session->get($request, self::CSRF_KEY) !== $data[self::CSRF_KEY]) {
            return $this->errorResponse($request, $response, self::EXCEPTION_IS_NOT_SAME);
        }

        return null;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $reasonPhrase
     * @return Response
     */
    private function errorResponse(Request $request, Response $response, string $reasonPhrase)
    {
        $this->session->set($request, self::CSRF_KEY, $this->csrfTokenGenerator->generate());

        return $this->responseHandler->errorResponse($request, $response, self::EXCEPTION_STATUS, $reasonPhrase);
    }
}
