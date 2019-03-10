<?php

declare(strict_types=1);

namespace Energycalculator\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;

final class LocaleMiddleware
{
    /**
     * @var AcceptLanguageNegotiatorInterface
     */
    private $acceptLanguageNegotiator;

    /**
     * @var string
     */
    private $localeFallback;

    /**
     * @param AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator
     * @param string                            $localeFallback
     */
    public function __construct(AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator, string $localeFallback)
    {
        $this->acceptLanguageNegotiator = $acceptLanguageNegotiator;
        $this->localeFallback = $localeFallback;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if ('/' === $request->getUri()->getPath()) {
            if (null !== $negotiatedValue = $this->acceptLanguageNegotiator->negotiate($request)) {
                $locale = $negotiatedValue->getValue();
            } else {
                $locale = $this->localeFallback;
            }

            return $response->withStatus(302)->withHeader('Location', '/'.$locale);
        }

        $response = $next($request, $response);

        return $response;
    }
}
