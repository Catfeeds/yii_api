<?php
namespace backend\modules\admin;

/**
 * 设置模块
 */
class admin extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
