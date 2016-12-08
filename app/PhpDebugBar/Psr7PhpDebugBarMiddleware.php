<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DebugBar;
use PhpMiddleware\PhpDebugBar\PhpDebugBarMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Psr7PhpDebugBarMiddleware extends PhpDebugBarMiddleware
{
    /**
     * @var DebugBar
     */
    protected $debugBar;

    /**
     * @param DebugBar $debugBar
     */
    public function __construct(DebugBar $debugBar)
    {
        $this->debugBar = $debugBar;

        $renderer = $debugBar->getJavascriptRenderer('/phpdebugbar');
        $renderer->setOpenHandlerUrl('/phpdebugbar-storage');

        parent::__construct($renderer);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->debugBar->addCollector(new Psr7RequestDataCollector($request));

        return parent::__invoke($request, $response, $next);
    }
}
