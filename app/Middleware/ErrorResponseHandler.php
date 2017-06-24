<?php

declare(strict_types=1);

namespace Energycalculator\Middleware;

use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ErrorResponseHandler
{
    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var TwigRender
     */
    private $twigRender;

    /**
     * @param TemplateData $templateData
     * @param TwigRender $twigRender
     */
    public function __construct(TemplateData $templateData, TwigRender $twigRender)
    {
        $this->templateData = $templateData;
        $this->twigRender = $twigRender;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $status
     * @param string $reasonPhrase
     * @return Response
     */
    public function errorResponse(Request $request, Response $response, int $status, string $reasonPhrase): Response
    {
        $response = $response->withStatus($status, $reasonPhrase);

        return $this->twigRender->render($response, '@Energycalculator/httpexception.html.twig', $this->templateData->aggregate($request, ['code' => $status, 'message' => $reasonPhrase]));
    }
}
