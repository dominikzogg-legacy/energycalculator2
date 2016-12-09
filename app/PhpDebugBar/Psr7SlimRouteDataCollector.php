<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Route;

/**
 * Collects info about the current request
 */
class Psr7SlimRouteDataCollector extends DataCollector implements Renderable
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

        if (null === $route = $request->getAttribute('route')) {
            return [];
        }

        $routeData['name'] = $route->getName();
        $routeData['pattern'] = $route->getPattern();
        $routeData['arguments'] = $route->getArguments();

        return [
            'name' => $formatter->formatVar($route->getName()),
            'pattern' => $formatter->formatVar($route->getPattern()),
            'arguments' => $formatter->formatVar($route->getArguments()),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'route';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            "route" => array(
                "icon" => "tags",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "route",
                "default" => "{}"
            )
        );
    }
}
