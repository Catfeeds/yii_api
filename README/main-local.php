<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=218.2.102.114:5933;dbname=qa_wemall',
//             'dsn' => 'mysql:host=127.0.0.1;dbname=dev_open',
            'username' => 'root',
            'password' => 'hogesoft',
//             'password' => '123456',
            'charset' => 'utf8',
            'tablePrefix' => 'pre_'
        ],
//         'mailer' => [
//             'class' => 'yii\swiftmailer\Mailer',
//             'viewPath' => '@common/mail',
//             // send all mails to a file by default. You have to set
//             // 'useFileTransport' to false and configure a transport
//             // for the mailer to send real emails.
//             'useFileTransport' => true,
//         ],
        'redis' => [
//          'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Connection',
            'hostname' => '10.0.1.66',
            'database' => '1',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
];
