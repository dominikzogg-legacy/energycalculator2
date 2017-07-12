<?php

declare(strict_types=1);

namespace Energycalculator\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;

final class HomeController
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
     * @param TwigRender   $twig
     */
    public function __construct(TemplateData $templateData, TwigRender $twig)
    {
        $this->templateData = $templateData;
        $this->twig = $twig;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function home(Request $request, Response $response)
    {
        return $this->twig->render($response, '@Energycalculator/home.html.twig', $this->templateData->aggregate($request));
    }
}
