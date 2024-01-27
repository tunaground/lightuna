<?php

namespace Lightuna\Controller;

use Lightuna\Controller\Action\CreateBoardController;
use Lightuna\Controller\Action\CreateResponseController;
use Lightuna\Controller\Action\CreateThreadController;
use Lightuna\Controller\Admin\AdminBoardController;
use Lightuna\Controller\Admin\AdminBoardDetailController;
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

}