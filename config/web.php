<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'sessia-marketplace',
    'name' => 'Marketplace',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
    ],
    'sourceLanguage' => 'ru',
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
       'gridview' =>  [
            'class' => '\kartik\grid\Module',
        ],
    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'lavrentiev\widgets\toastr\ToastrAsset' => [
                    'depends' => []
                ],
            ],
        ],
        'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => 'GXC1x64GGYNqLcN11WiVM1nySjXNph49',
        ],
        'cache' => [
            // 'class' => 'yii\caching\FileCache',
            'class' => 'yii\caching\DummyCache',
            'defaultDuration' => 86400,
        ],
        'user' => [
            'identityClass' => 'yii2mod\user\models\UserModel',
            // for update last login date for user, you can call the `afterLogin` event as follows
            'on afterLogin' => function ($event) {
                $event->identity->updateLastLogin();
            }
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'viewPath' => '@app/mail',
            'htmlLayout' => 'layouts/html',
            'textLayout' => 'layouts/text',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',
                'username' => 'sessia.marketplace@yandex.ru',
                'password' => 'marketplaceSessia',
                'port' => 465,
                'encryption' => 'ssl',
                'streamOptions' => [ 
                    'ssl' => [ 
                        'allow_self_signed' => true, 
                        'verify_peer' => false, 
                        'verify_peer_name' => false, 
                    ], 
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'forceTranslation' => true,
                    'sourceMessageTable' => '{{%source_message}}',
                    'messageTable' => '{{%message}}',
                    'enableCaching' => false,
                    // 'cachingDuration' => 3600,
                    'sourceLanguage' => 'ru'
                ],
                'yii2mod.user' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/user/messages',
                ],
            ],
        ],        
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '' => 'site/index',
                'products' => 'products/index',
                'products/<action>' => 'products/<action>',
                'orders' => 'orders/index',
                'orders/<action>' => 'orders/<action>',             
                'import' => 'import/index',
                'import/<from>' => 'import/index',
                '<action>'=>'site/<action>',
            ],
        ],
        
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@yii2mod/user/views' => '@app/views/user',
                ],
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
