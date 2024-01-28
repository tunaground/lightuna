<?php

namespace Lightuna\Controller\Action;

use Lightuna\Controller\AbstractController;
use Lightuna\Core\Context;
use Lightuna\Exception\QueryException;
use Lightuna\Http\HttpRequest;
use Lightuna\Http\HttpResponse;
use Lightuna\Object\Notice;
use Lightuna\Object\Response;
use Lightuna\Object\Thread;
use Lightuna\Service\AttachmentServiceInterface;
use Lightuna\Service\BoardServiceInterface;
use Lightuna\Service\ThreadServiceInterface;
use Lightuna\Util\TemplateRenderer;

class UpdateNoticeController extends AbstractController
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
        try {
            $logger = $this->context->getLogger();

            $notice = $this->boardService->getNoticeByNoticeId($httpRequest->getPost('id'));
            $notice->setContent($httpRequest->getPost('content'));
            $this->boardService->updateNotice($notice);

            $boardId = $notice->getBoardId();
            $logger->info("board({$boardId}) notice({$notice->getId()}) updated");
            $httpResponse->addHeader("Refresh:0; url=/admin/board/{$boardId}");
        } catch (QueryException $e) {
            $body = $this->templateRenderer->render('page/error.html', [
                'message' => 'database query error'
            ]);
            $httpResponse->setBody($body);
        } catch (\Throwable $e) {
            $body = $this->templateRenderer->render('page/error.html', [
                'message' => $e->getMessage()
            ]);
            $httpResponse->setBody($body);
        }
        return $httpResponse;
    }
}
