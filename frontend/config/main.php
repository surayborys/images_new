<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', [
        //class for selecting an interface language depending on an information in client cookies
        'class' => 'frontend\components\LanguageSelector',
    ]],
    'language' => 'en-US',
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
        ],
        'post' => [
            'class' => 'frontend\modules\post\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'frontend\models\User',
            'enableAutoLogin' => true,
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
                '/' => 'site/index',
                'profile/<nickname:\w+>' => 'user/profile/view',
                'user/profile/follow/<id:\w+>' =>  'user/profile/follow',
                'user/profile/unsubscribe/<id:\w+>' =>  'user/profile/unsubscribe',
                'user/profile/edit/<id:\w+>' => 'user/profile/edit',
                'user/profile/unset-picture/<id:\w+>' => 'user/profile/unset-picture',
                'post/<id:\w+>' => 'post/default/view',
                'post/delete/comment/<postId:\w+>/<commentId:\w+>' => 'post/default/delete-comment',
            ],
        ],
        
        
        'feedService' => [
            'class' => 'frontend\components\FeedService',
        ],
        
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
    ],
    'params' => $params,
];
