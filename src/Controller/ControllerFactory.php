<?php

namespace Lightuna\Controller;

use Lightuna\Controller\Action\CreateBoardController;
use Lightuna\Controller\Action\CreateResponseController;
use Lightuna\Controller\Action\CreateThreadController;
use Lightuna\Controller\Action\UpdateBoardController;
use Lightuna\Controller\Action\UpdateNoticeController;
use Lightuna\Controller\Admin\AdminBoardController;
use Lightuna\Controller\Admin\AdminBoardDetailController;
use Lightuna\Controller\API\V1\DeleteResponseController;
use Lightuna\Controller\API\V1\GetResponseController;
use Lightuna\Controller\API\V1\RestoreResponseController;
use Lightuna\Core\Context;
use Lightuna\Service\ServiceFactory;
use Lightuna\Util\TemplateRenderer;

class ControllerFactory
{
    public static function getCreateThreadController(Context $context): CreateThreadController
    {
        $config = $context->getConfig();
        return new CreateThreadController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
            ServiceFactory::getThreadService($config),
            ServiceFactory::getAttachmentService($config),
        );
    }

    public static function getIndexController(Context $context): IndexController
    {
        $config = $context->getConfig();
        return new IndexController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
            ServiceFactory::getThreadService($config),
        );
    }

    public static function getAdminBoardController(Context $context): AdminBoardController
    {
        $config = $context->getConfig();
        return new AdminBoardController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
        );
    }

    public static function getAdminBoardDetailController(Context $context): AdminBoardDetailController
    {
        $config = $context->getConfig();
        return new AdminBoardDetailController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
            ServiceFactory::getThreadService($config),
        );
    }

    public static function getCreateBoardController(Context $context): CreateBoardController
    {
        $config = $context->getConfig();
        return new CreateBoardController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
        );
    }

    public static function getCreateResponseController(Context $context): CreateResponseController
    {
        $config = $context->getConfig();
        return new CreateResponseController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
            ServiceFactory::getThreadService($config),
            ServiceFactory::getAttachmentService($config),
        );
    }

    public static function getUpdateNoticeController(Context $context): UpdateNoticeController
    {
        $config = $context->getConfig();
        return new UpdateNoticeController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
        );
    }

    public static function getUpdateBoardController(Context $context): UpdateBoardController
    {
        $config = $context->getConfig();
        return new UpdateBoardController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
        );
    }

    public static function getTraceController(Context $context): TraceController
    {
        $config = $context->getConfig();
        return new TraceController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getBoardService($config),
            ServiceFactory::getThreadService($config),
        );
    }

    public static function getDeleteResponseController(Context $context): DeleteResponseController
    {
        $config = $context->getConfig();
        return new DeleteResponseController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getThreadService($config),
        );
    }

    public static function getRestoreResponseController(Context $context): RestoreResponseController
    {
        $config = $context->getConfig();
        return new RestoreResponseController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getThreadService($config),
        );
    }

    public static function getGetResponseController(Context $context): GetResponseController
    {
        $config = $context->getConfig();
        return new GetResponseController(
            $context,
            new TemplateRenderer($config['site']['rootDir'] . '/template'),
            ServiceFactory::getThreadService($config),
        );
    }
}