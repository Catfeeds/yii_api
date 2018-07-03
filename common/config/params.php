<?php
return [
    'adminEmail' => 'admin@example.com',
//     'supportEmail' => 'support@example.com',
//     'user.passwordResetTokenExpire' => 3600,

    'kdniao' => [
         'ebusinessid' => '1313161', //商户号
         'appkey' => '08daa0de-633a-4ee7-8eff-c2ce965ab692', //密钥
    ],

    'sms' => [
        'signname' => '身边的好消息验证码',  //签名
        'scene'  => [  
            'register' => ['no' => '1', 'tpl'=> 'SMS_119081786'], //注册场景
            'forget_password' => ['no' => '2', 'tpl' => 'SMS_119081786'], //运用于忘记密码的场景
            'set_pay_password' => ['no' => '3', 'tpl' => 'SMS_119081786'], //设置支付密码
            'pay_order_ok'  => ['no' => '4', 'tpl' => 'SMS_119081786'], //付款成功
        ],
        'ip_limit'    => ['time' => 60,'num' => 6],//同一个IP,一分钟之内最多发送6次
        'phone_limit' => ['time' => 60,'num' => 2], //同一个手机号，一分钟之内最多发送2次
    ],

    //微信
    'WECHAT' =>
    [
        'debug'  => true,
        'app_id' => 'wx6e642015f3917b58',
        'secret' => 'f5464099028cb44a4c18d7cc9a794421',
        'MCHID'=>'1494323692',
        'KEY'=>'910208asd,./',
        'token'  => 'weixin',
        'aes_key' => null, // 可选
        'log' => [
            'level' => 'debug',
            'file'  => '/tmp/easywechat.log', // XXX: 绝对路径！！！！
        ],
    ],
    //百度地图
    'baidu' => [
        'ak' => 'EBnu4BEqoFYVppWf4G09quGNdsTkSPIK'
    ],   
        //易联云
    'yiliany'=>[
      'user_id'=>'20807',
      'user_name' => '15722926461',
      'api' => '0a40b40704c25786b95ec9e74921676866c87c3a',
    ]
];



