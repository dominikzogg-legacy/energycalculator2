<?php

namespace Energycalculator\PhpDebugBar;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class Psr7PhpDebugBarMiddleware
{
    /**
     * @var DebugBar
     */
    protected $debugBar;

    /**
     * @var JavascriptRenderer
     */
    protected $debugBarRenderer;

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

        $this->debugBarRenderer = $debugBar->getJavascriptRenderer('/phpdebugbar');
        $this->debugBarRenderer->setOpenHandlerUrl('/phpdebugbar-storage');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if ($staticFile = $this->getStaticFile($request->getUri())) {
            return $staticFile;
        }

        /** @var ResponseInterface $outResponse */
        $outResponse = $next($request, $response);

        $this->debugBar->addCollector(new Psr7SlimRouteDataCollector($request));
        $this->debugBar->addCollector(new Psr7RequestDataCollector($request));
        $this->debugBar->addCollector(new Psr7ResponseDataCollector($outResponse));

        foreach ($this->collectors as $collector) {
            $this->debugBar->addCollector($collector);
        }

        if (!$this->isHtmlAccepted($request)) {
            return $outResponse;
        }

        $debugBarHead = $this->debugBarRenderer->renderHead();
        $debugBarBody = $this->debugBarRenderer->render();

        if ($this->isHtmlResponse($outResponse)) {
            $body = $outResponse->getBody();
            if (!$body->eof() && $body->isSeekable()) {
                $body->seek(0, SEEK_END);
            }
            $body->write($debugBarHead.$debugBarBody);

            return $outResponse;
        }

        $outResponseBody = Response\Serializer::toString($outResponse);
        $template = '<html><head>%s</head><body><h1>DebugBar</h1><p>Response:</p><pre>%s</pre>%s</body></html>';
        $escapedOutResponseBody = htmlspecialchars($outResponseBody);
        $result = sprintf($template, $debugBarHead, $escapedOutResponseBody, $debugBarBody);

        return new Response\HtmlResponse($result);
    }

    /**
     * @param UriInterface $uri
     *
     * @return ResponseInterface|null
     */
    private function getStaticFile(UriInterface $uri)
    {
        $path = $this->extractPath($uri);

        if (strpos($path, $this->debugBarRenderer->getBaseUrl()) !== 0) {
            return;
        }

        $pathToFile = substr($path, strlen($this->debugBarRenderer->getBaseUrl()));

        $fullPathToFile = $this->debugBarRenderer->getBasePath().$pathToFile;

        if (!file_exists($fullPathToFile)) {
            return;
        }

        $contentType = $this->getContentTypeByFileName($fullPathToFile);
        $stream = new Stream($fullPathToFile, 'r');

        return new Response($stream, 200, [
            'Content-type' => $contentType,
        ]);
    }

    /**
     * @param UriInterface $uri
     *
     * @return string
     */
    private function extractPath(UriInterface $uri)
    {
        // Slim3 compatibility
        if ($uri instanceof Uri) {
            $basePath = $uri->getBasePath();
            if (!empty($basePath)) {
                return $basePath;
            }
        }

        return $uri->getPath();
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getContentTypeByFileName($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $map = [
            'css' => 'text/css',
            'js' => 'text/javascript',
            'otf' => 'font/opentype',
            'eot' => 'application/vnd.ms-fontobject',
            'svg' => 'image/svg+xml',
            'ttf' => 'application/font-sfnt',
            'woff' => 'application/font-woff',
            'woff2' => 'application/font-woff2',
        ];

        if (isset($map[$ext])) {
            return $map[$ext];
        }

        return 'text/plain';
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    private function isHtmlResponse(ResponseInterface $response)
    {
        return $this->hasHeaderContains($response, 'Content-Type', 'text/html');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function isHtmlAccepted(ServerRequestInterface $request)
    {
        return $this->hasHeaderContains($request, 'Accept', 'text/html');
    }

    /**
     * @param MessageInterface $message
     * @param string           $headerName
     * @param string           $value
     *
     * @return bool
     */
    private function hasHeaderContains(MessageInterface $message, $headerName, $value)
    {
        return strpos($message->getHeaderLine($headerName), $value) !== false;
    }
}
