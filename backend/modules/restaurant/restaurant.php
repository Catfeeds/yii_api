<?php
namespace backend\modules\restaurant;
use backend\modules\restaurant\controllers;
/**
 * 餐饮管理
 */
class restaurant extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\restaurant\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}

