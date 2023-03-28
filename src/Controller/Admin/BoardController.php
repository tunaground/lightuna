<?php

namespace Lightuna\Controller\Admin;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Dao\MariadbBoardDao;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
use Lightuna\Service\BoardService;
use Lightuna\Util\TemplateRenderer;

class BoardController extends AbstractController
{
    private BoardService $boardService;

    public function __construct(TemplateRenderer $templateRenderer, Context $context)
    {
        parent::__construct($templateRenderer, $context);
        $this->boardService = new BoardService(new MariadbBoardDao($this->context->getPdo()));
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        $boards = $this->boardService->getBoards();
        $boardList = array_reduce($boards, function (string $acc, Board $board) {
            return $acc . $this->templateRenderer->render('board_list_item.html', [
                'id' => $board->getBoardId(),
                'name' => $board->getName(),
                'deleted' => ($board->isDeleted())? 'Deleted' : 'Active',
                'created_at' => $board->getCreatedAt()->format(DATETIME_FORMAT),
                'updated_at' => $board->getUpdatedAt()->format(DATETIME_FORMAT),
                'deleted_at' => ($board->getDeletedAt() === null)
                ? "No"
                : $board->getDeletedAt()->format(DATETIME_FORMAT),
            ]);
        }, "");
        $createBoard = $this->templateRenderer->render('create_board.html');
        $body = $this->templateRenderer->render('page/admin/board.html', [
            'create_board' => $createBoard,
            'board_list' => $boardList,
        ]);
        $httpResponse->setBody($body);
        return $httpResponse;
    }
}
