<?php
namespace backend\modules\wechat;
/**
 * 微信管理
 */
class wechat extends \yii\base\Module
{
    public $enableCsrfValidation  = false;
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\wechat\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
