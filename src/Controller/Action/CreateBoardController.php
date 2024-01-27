<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
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
        $dateTime = new \DateTime();
        $board = new Board();
        $board->setId($httpRequest->getPost("id"));
        $board->setName($httpRequest->getPost("name"));
        $board->setDeleted(false);
        $board->setCreatedAt($dateTime);
        $board->setUpdatedAt($dateTime);
        $this->boardService->createBoard($board);
        $httpResponse->addHeader("Refresh:0; url=/admin/boards");
        return $httpResponse;
    }
}
