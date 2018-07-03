<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//         'css/site.css',
        'css/app-dcd9c69da389564472499d2ae717fcf2.css',
        'css/app-bar-01b555d54a1443f680a4f19b77423506.css',
        'css/vendor-5f7be43adb992f6c9533832b3eba1232.css'
    ];
    public $js = [
        'plugins/jQuery/jQuery-2.2.0.min.js',
    ];
    public $depends = [
//         'yii\web\YiiAsset',
//         'yii\bootstrap\BootstrapAsset',
    ];
}
