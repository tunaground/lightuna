<?php

use Lightuna\Middleware\Blocker\Blocker;
use Lightuna\Middleware\Blocker\Rule\CountryRule;
use Lightuna\Middleware\Blocker\Rule\SessionAuthRule;
use Lightuna\Middleware\Operator\Location;

return [
    'site' => [
        'domain' => 'localhost',
        'baseUrl' => '/lightuna',
        'defaultBoard' => 'tuna',
        'environment' => 'dev',
        'imageUploadPrefix' => '/upload',
        'imageUploadPath' => __DIR__ . '/../upload',
        'allowFileType' => ['image/png', 'image/jpg', 'image/jpeg'],
        'logFilePath' => __DIR__ . '/../logs/info.log'
    ],
    'middleware' => [
        new Location(
            false,
            [
                '/lightuna/index.php',
                '/lightuna/trace.php',
                '/lightuna/post.php',
                '/lightuna/console.php'
            ],
            new Blocker(
                false,
                new CountryRule(true),
                new SessionAuthRule(true)
            )
        )
    ],
    'database' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'lightuna',
        'password' => 'lightuna',
        'schema' => 'lightuna',
        'options' => [
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ]
    ],
    'boards' => [
        '__default__' => [
            'userName' => '이름 없음',
            'maxThreadView' => 5,
            'maxThreadListView' => 15,
            'maxResponseView' => 30,
            'maxResponseLineView' => 50,
            'maxTitleLength' => 50,
            'maxNameLength' => 60,
            'maxContentLength' => 20000,
            'maxResponseSize' => 1000,
            'maxResponseInterval' => 3,
            'maxDuplicateResponseInterval' => 10,
            'maxImageSize' => 1 * 1024 * 1024,
            'maxImageNameLength' => 80,
            'style' => 'default.css'
        ],
        'develop' => [
            'uid' => 'develop',
            'name' => '테스트용',
            'userName' => '익명의 테스터'
        ]
    ],
    'blocker' => [
        'allowCountry' => [
            'KR'
        ],
        'sessionAuth' => [
            'key' => 'any-string-here'
        ]
    ]
];
