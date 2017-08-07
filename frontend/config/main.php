<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute'=>'index.html',
    //设置语言
    'language'=>'zh-CN',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            // 'identityClass' => 'common\models\User',
            'identityClass' => \frontend\models\Member::className(),
            'enableAutoLogin' => true,
            'loginUrl'=>'/member/login',
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'sms'=>[
            'class'=>\frontend\components\AliyunSms::className(),
            'accessKeyId'=>'LTAIR7ndqp1uiAH1',
            'accessKeySecret'=>'D6AFkDs1XgVcpvW2GgekHpxrastVfM',
            'signName'=>'彭梓昕',
            'templateCode'=>'SMS_80035065'
        ]
    ],
    'params' => $params,
];
