<?php

namespace Energycalculator\Controller\Crud;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ViewController
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @param string $type
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface $authorization
     * @param ErrorResponseHandler $errorResponseHandler
     * @param RepositoryInterface $repository
     * @param TemplateData $templateData
     * @param TwigRender $twig
     */
    public function __construct(
        string $type,
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        ErrorResponseHandler $errorResponseHandler,
        RepositoryInterface $repository,
        TemplateData $templateData,
        TwigRender $twig
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->errorResponseHandler = $errorResponseHandler;
        $this->repository = $repository;
        $this->templateData = $templateData;
        $this->type = $type;
        $this->twig = $twig;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var ModelInterface $element */
        $element = $this->repository->find($id);
        if (null === $element) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                404,
                sprintf('%s.error.notfound', strtolower($this->type))
            );
        }

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);
        if (!$this->authorization->isGranted($authenticatedUser, sprintf('%s_VIEW', strtoupper($this->type)), $element)) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                sprintf('%s.error.permissiondenied', strtolower($this->type))
            );
        }

        return $this->twig->render($response, sprintf('@Energycalculator/%s/view.html.twig', strtolower($this->type)),
            $this->templateData->aggregate($request, [
                'element' => prepareForView($element),
            ])
        );
    }
}
