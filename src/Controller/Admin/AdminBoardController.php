<?php

namespace Lightuna\Controller\Admin;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Board;
use Lightuna\Service\BoardServiceInterface;
use Lightuna\Util\TemplateRenderer;

class AdminBoardController extends AbstractController
{
    private BoardServiceInterface $boardService;

    public function __construct(
        Context               $context,
        TemplateRenderer      $templateRenderer,
        BoardServiceInterface $boardService,
    )
    {
        parent::__construct($context, $templateRenderer);
        $this->boardService = $boardService;
    }

    public function run(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse
    {
        $boards = $this->boardService->getBoards();
        $boardList = array_reduce($boards, function (string $acc, Board $board) {
            $status = ($board->getDeletedAt() === null)
                ? "Active"
                : "Deleted";
            return $acc . $this->templateRenderer->render('board_list_item.html', [
                    'id' => $board->getId(),
                    'name' => $board->getName(),
                    'deleted' => $status,
                    'created_at' => $board->getCreatedAt()->format(DATETIME_FORMAT),
                    'updated_at' => $board->getUpdatedAt()->format(DATETIME_FORMAT),
                    'deleted_at' => ($board->getDeletedAt() === null)
                        ? "No"
                        : $board->getDeletedAt()->format(DATETIME_FORMAT),
                ]);
        }, "");
        $createBoard = $this->templateRenderer->render('create_board.html');
        $body = $this->templateRenderer->render('page/admin/boards.html', [
            'create_board' => $createBoard,
            'board_list' => $boardList,
        ]);
        $httpResponse->setBody($body);
        return $httpResponse;
    }
}
