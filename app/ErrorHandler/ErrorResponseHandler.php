<?php

namespace Energycalculator\ErrorHandler;

use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ErrorResponseHandler
{
    const STATUS_400 = 'Bad Request';
    const STATUS_401 = 'Unauthorized';
    const STATUS_402 = 'Payment Required';
    const STATUS_403 = 'Forbidden';
    const STATUS_404 = 'Not Found';
    const STATUS_405 = 'Method Not Allowed';
    const STATUS_406 = 'Not Acceptable';
    const STATUS_407 = 'Proxy Authentication Required';
    const STATUS_408 = 'Request Time-out';
    const STATUS_409 = 'Conflict';
    const STATUS_410 = 'Gone';
    const STATUS_411 = 'Length Required';
    const STATUS_412 = 'Precondition Failed';
    const STATUS_413 = 'Request Entity Too Large';
    const STATUS_414 = 'Request-URL Too Long';
    const STATUS_415 = 'Unsupported Media Type';
    const STATUS_416 = 'Requested range not satisfiable';
    const STATUS_417 = 'Expectation Failed';
    const STATUS_418 = 'I’m a teapot';
    const STATUS_420 = 'Policy Not Fulfilled';
    const STATUS_421 = 'Misdirected Request';
    const STATUS_422 = 'Unprocessable Entity';
    const STATUS_423 = 'Locked';
    const STATUS_424 = 'Failed Dependency';
    const STATUS_425 = 'Unordered Collection';
    const STATUS_426 = 'Upgrade Required';
    const STATUS_428 = 'Precondition Required';
    const STATUS_429 = 'Too Many Requests';
    const STATUS_431 = 'Request Header Fields Too Large';
    const STATUS_451 = 'Unavailable For Legal Reasons';
    const STATUS_444 = 'No Response';
    const STATUS_449 = 'The request should be retried after doing the appropriate action';

    const STATUS_500 = 'Internal Server Error';
    const STATUS_501 = 'Not Implemented';
    const STATUS_502 = 'Bad Gateway';
    const STATUS_503 = 'Service Unavailable';
    const STATUS_504 = 'Gateway Time-out';
    const STATUS_505 = 'HTTP Version not supported';
    const STATUS_506 = 'Variant Also Negotiates';
    const STATUS_507 = 'Insufficient Storage';
    const STATUS_508 = 'Loop Detected';
    const STATUS_509 = 'Bandwidth Limit Exceeded';
    const STATUS_510 = 'Not Extended';
    const STATUS_511 = 'Network Authentication Required';

    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @param TemplateData $templateData
     * @param TwigRender   $twig
     */
    public function __construct(TemplateData $templateData, TwigRender $twig)
    {
        $this->templateData = $templateData;
        $this->twig = $twig;
    }

    /**
     * @param Request     $request
     * @param Response    $response
     * @param int         $code
     * @param string|null $message
     *
     * @return Response
     */
    public function errorReponse(Request $request, Response $response, int $code, string $message = null): Response
    {
        $reasonPhrase = self::getMessageByStatus($code);

        return $this->twig->render($response, '@Energycalculator/error.html.twig',
            $this->templateData->aggregate($request, ['code' => $code, 'message' => $message ?? $reasonPhrase])
        )->withStatus($code, $reasonPhrase);
    }

    /**
     * @param int $status
     *
     * @return string
     */
    private static function getMessageByStatus(int $status): string
    {
        $statusConstantName = 'STATUS_'.$status;
        $reflection = new \ReflectionClass(self::class);
        if ($reflection->hasConstant($statusConstantName)) {
            return $reflection->getConstant($statusConstantName);
        }

        return 'unknown';
    }
}
