<?php

namespace Energycalculator\Security;

use Chubbyphp\Security\Authentication\AuthenticationErrorHandlerInterface;
use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthenticationErrorHandler implements AuthenticationErrorHandlerInterface
{
    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @param TemplateData $templateData
     * @param TwigRender $twig
     */
    public function __construct(TemplateData $templateData, TwigRender $twig)
    {
        $this->templateData = $templateData;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $code
     * @return Response
     */
    public function errorResponse(Request $request, Response $response, int $code): Response
    {
        return $this->twig->render($response, '@Energycalculator/httpexception.html.twig',
            $this->templateData->aggregate($request, ['code' => $code])
        )->withStatus($code);
    }
}
