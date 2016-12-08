<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Route;

/**
 * Collects info about the current request
 */
class Psr7RequestDataCollector extends DataCollector implements Renderable
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function collect()
    {
        $formatter = $this->getDataFormatter();
        $request = $this->request;

        $routeData = [];
        if (null !== $route = $request->getAttribute('route')) {
            $routeData['name'] = $route->getName();
            $routeData['pattern'] = $route->getPattern();
            $routeData['arguments'] = $route->getArguments();
        }

        return [
            'method' => $formatter->formatVar($request->getMethod()),
            'requestTarget' => $formatter->formatVar($request->getRequestTarget()),
            'protocolVersion' => $formatter->formatVar($request->getProtocolVersion()),
            'headers' => $formatter->formatVar($request->getHeaders()),
            'body' => $formatter->formatVar($request->getParsedBody()),
            'route' => $formatter->formatVar($routeData),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'request';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            "request" => array(
                "icon" => "tags",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "request",
                "default" => "{}"
            )
        );
    }
}
