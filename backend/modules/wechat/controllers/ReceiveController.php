<?php
namespace backend\modules\wechat\controllers;

use Yii;
use backend\base\BaseController;

/**
 * 微信消息管理
 */
class ReceiveController extends BaseController
{
    public function actionSend()
    {
        $site_id = $this->getSite();
        $media_id = Yii::$app->request->post('media_id');
    }
}