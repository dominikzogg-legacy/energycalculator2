<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DebugBarException;

/**
 * Collects info about the request duration as well as providing
 * a way to log duration of any operations.
 */
class TimeDataCollector extends DataCollector implements Renderable
{
    /**
     * @var float
     */
    protected $requestStartTime;

    /**
     * @var float
     */
    protected $requestEndTime;

    /**
     * @param float $requestStartTime
     */
    public function __construct($requestStartTime = null)
    {
        if ($requestStartTime === null) {
            if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
                $requestStartTime = $_SERVER['REQUEST_TIME_FLOAT'];
            } else {
                $requestStartTime = microtime(true);
            }
        }
        $this->requestStartTime = $requestStartTime;
    }

    /**
     * Returns the request start time.
     *
     * @return float
     */
    public function getRequestStartTime()
    {
        return $this->requestStartTime;
    }

    /**
     * Returns the request end time.
     *
     * @return float
     */
    public function getRequestEndTime()
    {
        return $this->requestEndTime;
    }

    /**
     * Returns the duration of a request.
     *
     * @return float
     */
    public function getRequestDuration()
    {
        if ($this->requestEndTime !== null) {
            return $this->requestEndTime - $this->requestStartTime;
        }

        return microtime(true) - $this->requestStartTime;
    }

    /**
     * @return array
     *
     * @throws DebugBarException
     */
    public function collect()
    {
        $this->requestEndTime = microtime(true);

        return array(
            'start' => $this->requestStartTime,
            'end' => $this->requestEndTime,
            'duration' => $this->getRequestDuration(),
            'duration_str' => $this->getDataFormatter()->formatDuration($this->getRequestDuration()),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'time';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            'time' => array(
                'icon' => 'clock-o',
                'tooltip' => 'Request Duration',
                'map' => 'time.duration_str',
                'default' => "'0ms'",
            ),
        );
    }
}
