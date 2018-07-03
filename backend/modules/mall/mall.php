<?php
namespace backend\modules\mall;
use backend\modules\mall\controllers;
/**
 * 商城管理
 */
class mall extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\mall\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
//        $site_key = 1;
//        $options = SiteController::actionSiteview($site_key);
//        \common\extensions\Wechat\Loader::config($options);

//         if (!isset(Yii::$app->i18n->translations['forum'])) {
//             Yii::$app->i18n->translations['forum'] = [
//                 'class' => 'yii\i18n\PhpMessageSource',
//                 'sourceLanguage' => 'en',
//                 'basePath' => '@backend/modules/forum/messages'
//             ];
//         }

    }
}

