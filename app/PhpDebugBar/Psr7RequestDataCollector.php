<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Collects info about the current request.
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

        $headerData = [];
        foreach (array_keys($request->getHeaders()) as $headerName) {
            if (0 === stripos($headerName, 'host') || 0 === stripos($headerName, 'http_cookie')) {
                continue;
            }
            $headerData[$headerName] = $request->getHeaderLine($headerName);
        }

        return [
            'method' => $formatter->formatVar($request->getMethod()),
            'requestTarget' => $formatter->formatVar($request->getRequestTarget()),
            'protocolVersion' => $formatter->formatVar($request->getProtocolVersion()),
            'host' => $formatter->formatVar($request->getHeaderLine('Host')),
            'headers' => $formatter->formatVar($headerData),
            'cookies' => $formatter->formatVar($request->getCookieParams()),
            'body' => $formatter->formatVar($request->getParsedBody()),
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
            'request' => array(
                'icon' => 'tags',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'request',
                'default' => '{}',
            ),
        );
    }
}
