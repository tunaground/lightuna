<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Dao\MariadbBoardDao;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
use Lightuna\Service\BoardService;
use Lightuna\Util\TemplateRenderer;

class CreateBoardController extends AbstractController
{
    private BoardService $boardService;

    public function __construct(TemplateRenderer $templateRenderer, Context $context)
    {
        parent::__construct($templateRenderer, $context);
        $this->boardService = new BoardService(new MariadbBoardDao($context->getPdo()));
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
