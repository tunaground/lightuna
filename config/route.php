<?php

use Lightuna\Core\Context;
use Lightuna\Controller\ControllerFactory;
use Lightuna\Controller\ControllerInterface;

return [
    [
        'path' => '/',
        'redirect' => '/admin',
    ],
    [
        'path' => '/index/:boardId',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getIndexController($context);
        }
    ],
    [
        'path' => '/action/create/thread',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getCreateThreadController($context);
        }
    ],
    [
        'path' => '/action/update/notice',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getUpdateNoticeController($context);
        },
    ],
    [
        'path' => '/action/update/board',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getUpdateBoardController($context);
        },
    ],
    [
        'path' => '/admin/boards',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getAdminBoardController($context);
        },
    ],
    [
        'path' => '/admin/board/:boardId',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getAdminBoardDetailController($context);
        }
    ],
    [
        'path' => '/admin',
        'redirect' => '/admin/boards',
    ],
    [
        'path' => '/action/create/board',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getCreateBoardController($context);
        },
    ],
    [
        'path' => '/action/create/response',
        'controller' => function (Context $context): ControllerInterface {
            return ControllerFactory::getCreateResponseController($context);
        }
    ],
];
