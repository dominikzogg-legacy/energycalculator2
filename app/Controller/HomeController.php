<?php

declare(strict_types=1);

namespace Energycalculator\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Energycalculator\Service\TwigRender;

final class HomeController
{
    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @param TwigRender   $twig
     */
    public function __construct(TwigRender $twig)
    {
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
        return $this->twig->render($response, '@Energycalculator/home.html.twig', $this->twig->aggregate($request));
    }
}
