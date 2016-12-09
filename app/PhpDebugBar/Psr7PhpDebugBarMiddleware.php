<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DataCollector\DataCollectorInterface;
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
     * @var DataCollectorInterface[]
     */
    protected $collectors;

    /**
     * @param DebugBar $debugBar
     */
    public function __construct(DebugBar $debugBar, array $collectors)
    {
        $this->debugBar = $debugBar;
        $this->collectors = $collectors;

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
        $this->debugBar->addCollector(new Psr7SlimRouteDataCollector($request));
        $this->debugBar->addCollector(new Psr7RequestDataCollector($request));
        $this->debugBar->addCollector(new Psr7ResponseDataCollector($response));

        foreach ($this->collectors as $collector) {
            $this->debugBar->addCollector($collector);
        }

        return parent::__invoke($request, $response, $next);

    }
}
