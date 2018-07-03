<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'dashboard' => [
            'class' => 'backend\modules\dashboard\dashboard', //主页
        ],
        'wechat' => [
            'class' => 'backend\modules\wechat\wechat',  //微信
        ],
        'cms' => [
            'class' => 'backend\modules\cms\cms',   //内容管理
        ],
        'mall' => [
            'class' => 'backend\modules\mall\mall',  //商城
        ],
        'order' => [
            'class' => 'backend\modules\order\order',
        ],
        'comment' => [
            'class' => 'backend\modules\comment\comment', //评论
        ],
        'ucenter' => [
            'class' => 'backend\modules\ucenter\ucenter', //用户中心
        ],
        'admin' => [
            'class' => 'backend\modules\admin\admin', //站点
        ],
	    'activity' => [
	    	'class' => 'backend\modules\admin\activity', //营销活动
	    ],
    	'attachment' => [
    			'class' => 'backend\modules\attachment\attachment', //文件
    	],
        'shipping' => [
                'class' => 'backend\modules\shipping\shipping', //物流
        ],
        'sms' => [
                'class' => 'backend\modules\sms\sms', //短信
        ],
        'cms' => [
                'class' => 'backend\modules\cms\cms', //文档系统
        ],
        'restaurant' => [
                'class' => 'backend\modules\restaurant\restaurant', //餐厅
        ],
        'analysis' => [
            'class' => 'backend\modules\analysis\analysis', //数据分析
        ],
        'rabc' => [
            'class' => 'backend\modules\rabc\rabc', //数据分析
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'view' => [
            'theme' => [
                'basePath' => '@app/themes/basic',
                'baseUrl' => '@web/themes/basic',
//                 'pathMap' => [
//                     '@app/views' => '@app/themes/basic',
//                 ],
            ],
        ],
//         'authManager' => [
//             'class' => 'yii\rbac\DbManager',
//         ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => '/dashboard/index/index',
//                 '<module>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
//                 '<id:[\x{4e00}-\x{9fa5}a-zA-Z0-9_]*>' => 'user/view',
            ],
        ],
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
        ],
        //短信
        'sms' => [
            'class' => 'common\extensions\aliyunsms\SMSMessage',
            'endPoint' => 'http://31231402.mns.cn-shanghai.aliyuncs.com',
            'accessId' => 'S8Q4N7VR9jICCp3B',
            'accessKey' => 'gZGRoi0IEwE1lmGX9TTfTXSPWqyK3m',
        ],
    ],
    'params' => $params,
];
