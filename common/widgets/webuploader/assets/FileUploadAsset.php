<?php
/**
 * @link http://www.yii-china.com/
 * @copyright Copyright (c) 2015 Yii中文网
 */

namespace common\widgets\webuploader\assets;

use Yii;
use yii\web\AssetBundle;

class FileUploadAsset extends AssetBundle
{
    public $css = [
        'css/style.css',
        'css/webuploader.css',
    ];
    
    public $js = [
        'js/upload.js',
        'js/Uploader.swf',
        'js/webuploader.min.js',
    ];
    
    public $depends = [
//         'yii\web\YiiAsset',
    ];
    
    /**
     * 初始化：sourcePath赋值
     * @see \yii\web\AssetBundle::init()
     */
    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR . 'statics';
    }
}