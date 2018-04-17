<?php
define('YII_DEBUG', true);
define('YII_ENV', 'production');

$db  = require __DIR__ . '/../config/db.php';
$acl = require __DIR__ . '/../config/acl.php';
$def = require __DIR__ . '/../config/def.php';

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../vendor/yiisoft/yii2/Yii.php';

( new yii\web\Application([
    'id'               => 'ascetic', // I think so
    'basePath'         => dirname(__DIR__),
    'params'	       => [
        'acl' => $acl,
        'def' => $def
    ],
    'timeZone'         => 'EET',
    'bootstrap'        => ['log', 'routes'],
    'components'       => [
        'route' => ['class'     => 'cyneek\yii2\routes\components\route'],
        'db'  => $db,
        'request'       => [
            'cookieValidationKey'  => '9988774jvdsjlsdkjgljlk435jk',
            'enableCsrfValidation' => false,             /* for POST requests */
            'parsers'              => [ 'application/json' => 'yii\web\JsonParser' ]
        ],
        'log' => [
            'traceLevel' => 0,
            'targets'    => [['class'  => 'yii\log\FileTarget', 'levels' => ['error','info'] ]]
        ]
    ],
    'modules' => [
        'routes' => [
            'class'      => 'cyneek\yii2\routes\Module',
            'routes_dir' => ['../aroutes'],
        ],
    ]
]) )->run();