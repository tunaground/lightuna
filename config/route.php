<?php

use Lightuna\Controller;

return [
    [
        'path' => '/',
        'redirect' => '/admin',
    ],
    [
        'path' => '/index/:boardId',
        'controller' => Controller\IndexController::class,
    ],
    [
        'path' => '/action/create/thread',
        'controller' => Controller\Action\CreateThreadController::class,
    ],
    [
        'path' => '/admin/boards',
        'controller' => Controller\Admin\AdminBoardController::class,
    ],
    [
        'path' => '/admin/board/:boardId',
        'controller' => Controller\Admin\AdminBoardDetailController::class,
    ],
    [
        'path' => '/admin',
        'redirect' => '/admin/boards',
    ],
    [
        'path' => '/action/create/board',
        'controller' => Controller\Action\CreateBoardController::class,
    ],
    [
        'path' => '/action/create/response',
        'controller' => Controller\Action\CreateResponseController::class,
    ],
];
