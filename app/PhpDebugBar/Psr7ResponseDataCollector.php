<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Collects info about the current response.
 */
class Psr7ResponseDataCollector extends DataCollector implements Renderable
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function collect()
    {
        $formatter = $this->getDataFormatter();
        $response = $this->response;

        $headerData = [];
        foreach (array_keys($response->getHeaders()) as $headerName) {
            $headerData[$headerName] = $response->getHeaderLine($headerName);
        }

        return [
            'protocolVersion' => $formatter->formatVar($response->getProtocolVersion()),
            'statusCode' => $formatter->formatVar($response->getStatusCode()),
            'reasonPhrase' => $formatter->formatVar($response->getReasonPhrase()),
            'headers' => $formatter->formatVar($headerData),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'response';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            'response' => array(
                'icon' => 'tags',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'response',
                'default' => '{}',
            ),
        );
    }
}
