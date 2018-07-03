<?php
namespace backend\modules\sms\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\sms\service\SMSService;

class SmsController extends BaseController
{

    public function actionSendsms()
    {
        $mobile = Yii::$app->request->post('mobile');
        $status = Yii::$app->request->post('status');
        if(SMSService::sendSmswithnum($mobile,$status)){
            return $this->jsonSuccess([$mobile],'发送成功');
        }else{
            return $this->jsonFail([],'发送失败');
        }
    }
}
