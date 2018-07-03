<?php

/**
 * @date        : 2017年8月30日
 * @author      : sun
 * @copyright   : http://www.hoge.cn/
 * @description : 短信发送
 */
namespace common\utils;

use yii;
use common\utils\StringUtil;
use common\models\SmsLog;

class SmsUtils
{

    public $scene;

    public $code;

    // 发送验证码
    public static function send($mobile, $SMSTemplateCode)
    {
        $code = StringUtil::random(4);
        $signname = Yii::$app->params['sms']['signname'];
        $status = Yii::$app->sms->send($mobile, $SMSTemplateCode['tmp'], $code, $signname);
        $SmsLog = new SmsLog();
        if ($status) {
            $SmsLog->mobile = $mobile;
            $SmsLog->session_id = $SMSTemplateCode['tmp'];
            $SmsLog->created_at = time();
            $SmsLog->endtime = 60 * 5 + time();
            $SmsLog->code = $code;
            $SmsLog->status = '1';
            $SmsLog->msg = '';
            $SmsLog->scene = $SMSTemplateCode['no'];
            $SmsLog->save();
            return true;
        } else {
            $SmsLog->error_msg = $status;
            return false;
        }
    }

    // 验证验证码
    //
    public static function validate($mobile, $SMSTemplateCode, $code)
    {
        $SmsLog = new SmsLog();
        $log = $SmsLog::find()->where('mobile=:mobile and scene=:scene and code =:code', [
            'mobile' => $mobile,
            'scene' => $SMSTemplateCode['no'],
            'code' => $code
        ])
            ->orderBy('created_at DESC')
            ->one();
        if ($log) {
            if (time() < $log->endtime) {
                return [
                    'code' => '0',
                    'msg' => '验证成功'
                ];
            } elseif (time() > $log->endtime) {
                return [
                    'code' => '1',
                    'msg' => '验证码已过期'
                ];
            } else {
                return [
                    'code' => '2',
                    'msg' => '验证失败'
                ];
            }
        } else {
            return [
                'code' => '0',
                'msg' => '验证失败'
            ];
        }
    }
}


