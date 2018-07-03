<?php
namespace backend\modules\rabc;
/**
 * 用户中心
 */
class rabc extends \yii\base\Module
{

    public $enableCsrfValidation  = false;
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\rabc\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
