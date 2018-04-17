<?php

return [
    'class'     => 'yii\db\Connection',
    'dsn'       => "pgsql:host=127.0.0.1;dbname=sb_dev",
    'username'  => 'sb',
    'password'  => 'bsbsbs',
    'schemaMap' => ['pgsql' => 'tigrov\pgsql\Schema']
];
