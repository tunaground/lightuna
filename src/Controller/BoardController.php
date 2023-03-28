<?php

namespace Lightuna\Controller;

use Lightuna\Core\Context;
use Lightuna\Dao\MariadbBoardDao;
use Lightuna\Dao\MariadbThreadDao;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
use Lightuna\Object\Thread;
use Lightuna\Service\BoardService;
use Lightuna\Service\ThreadService;
use Lightuna\Util\TemplateRenderer;

class BoardController extends AbstractController
{
    private BoardService $boardService;

    public function __construct(TemplateRenderer $templateRenderer, Context $context)
    {
        parent::__construct($templateRenderer, $context);
        $this->boardService = new BoardService(new MariadbBoardDao($context->getPdo()));
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        $dateTime = new \DateTime();
        $board = new Board();
        $board->setName($httpRequest->getPost("name"));
        $board->setThreadLimit($httpRequest->getPost('thread_limit'));
        $board->setDeleted(false);
        $board->setCreatedAt($dateTime);
        $board->setUpdatedAt($dateTime);
        $this->boardService->createBoard($board);
        $httpResponse->addHeader("Refresh:0; url=/admin.php");
        return $httpResponse;
    }
}
