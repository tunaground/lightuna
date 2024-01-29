<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
use Lightuna\Object\Notice;
use Lightuna\Service\BoardServiceInterface;
use Lightuna\Util\TemplateRenderer;

class CreateBoardController extends AbstractController
{
    private BoardServiceInterface $boardService;

    public function __construct(Context $context, TemplateRenderer $templateRenderer, BoardServiceInterface $boardService)
    {
        parent::__construct($context, $templateRenderer);
        $this->boardService = $boardService;
    }

    /**
     * @throws QueryException
     */
    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        $this->boardService->createBoard(
            $httpRequest->getPost('id'),
            $httpRequest->getPost('name'),
        );
        $httpResponse->addHeader("Refresh:0; url=/admin/boards");
        return $httpResponse;
    }
}
