<?php

use Lightuna\Controller;

return [
    [
        'path' => '/',
<<<<<<< HEAD
        'redirect' => '/index/tuna',
    ],
    [
        'path' => '/index/:bbsId',
        'controller' => Controller\IndexController::class,
    ],
=======
        'redirect' => '/admin.php',
    ],
    [
        'path' => '/index.php/:boardName',
        'controller' => Controller\IndexController::class,
    ],
    [
        'path' => '/thread.php',
        'controller' => Controller\ThreadController::class,
    ],
    [
        'path' => '/admin.php/board',
        'controller' => Controller\Admin\BoardController::class,
    ],
    [
        'path' => '/admin.php/thread/:boardName',
        'controller' => Controller\Admin\ThreadController::class,
    ],
    [
        'path' => '/admin.php',
        'redirect' => '/admin.php/board',
    ],
    [
        'path' => '/board.php',
        'controller' => Controller\BoardController::class,
    ],
    [
        'path' => '/response.php',
        'controller' => Controller\ResponseController::class,
    ],
>>>>>>> develop2
];
