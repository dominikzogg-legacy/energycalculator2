<?php

namespace Energycalculator\Csrf;

use Chubbyphp\Csrf\CsrfErrorHandlerInterface;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CsrfErrorHandler implements CsrfErrorHandlerInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $code
     * @param string $reasonPhrase
     * @return Response
     */
    public function errorResponse(Request $request, Response $response, int $code, string $reasonPhrase): Response
    {
        $this->session->addFlash($request, new FlashMessage(FlashMessage::TYPE_DANGER, $reasonPhrase));

        $host = $request->getHeaderLine('Host');
        $referer = $request->getHeaderLine('Referer');

        if ($host === parse_url($referer, PHP_URL_HOST)) {
            return $response
                ->withStatus(301)
                ->withHeader('Location', $referer);
        }

        throw \Chubbyphp\ErrorHandler\HttpException::create($request, $response, $code, $reasonPhrase);
    }
}
