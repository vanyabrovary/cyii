<?php

$db  = require __DIR__ . '/db.php';
$acl = require __DIR__ . '/acl.php';
$def = require __DIR__ . '/def.php';

return [
    'id' 		  => 'basic-console',
    'basePath' 		  => dirname(__DIR__),
    'bootstrap' 	  => ['log'],
    'controllerNamespace' => 'app\controllers',
    'timeZone'  	  => 'EET',
    'enableCoreCommands'  => 'false',

    'params' => [
        'acl' => $acl,
        'def' => $def
    ],

    'components' => [
	'db'  => $db,
        'log' => [
            'traceLevel' => 0,
            'targets' => [[
                'class'  => 'yii\log\FileTarget',
                'levels' => ['error','info'],
            ]]
        ]
    ]
];
