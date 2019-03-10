<?php

declare(strict_types=1);

namespace Energycalculator\Service;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouterInterface;

final class RedirectForPath
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Response $response
     * @param int      $status
     * @param string   $path
     * @param array    $arguments
     *
     * @return Response
     */
    public function get(Response $response, int $status, string $path, array $arguments = []): Response
    {
        return $response->withStatus($status)->withHeader('Location', $this->router->pathFor($path, $arguments));
    }
}
