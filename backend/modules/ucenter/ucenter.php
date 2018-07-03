<?php
namespace backend\modules\ucenter;
/**
 * 用户中心
 */
class ucenter extends \yii\base\Module
{

    public $enableCsrfValidation  = false;
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\ucenter\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
