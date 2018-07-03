<?php
/**
 * @date        : 2017年8月30日
 * @author      : Jason
 * @copyright   : http://www.ixn100.com/
 * @description : 短信功能
 */
namespace backend\modules\ucenter\service;

use yii;
use common\utils\StringUtil;
use backend\modules\ucenter\models\SmsLog;

class Sms
{

    public $scene;

    public $code;

    public $site_id;
    
    public static function getconfig($site_id)
    {
        return Yii::$app->params['sms'];
    }
    
    // 发送验证码
    // 需要添加安全过滤
    // 手机号,场景
    public static function send($mobile, $tpl)
    {
        $site_id = '1';
        $config = self::getconfig($site_id);
        $code = StringUtil::random(4);
        $signname = $config['signname'];
        //debug不发送短信
        if(YII_DEBUG){
            $status = '1';
        }else{
            $status = Yii::$app->sms->send($mobile, $config['scene']['register']['tpl'], $code, $signname);
        }
        $SmsLog = new SmsLog();
        if ($status) {
            $SmsLog->mobile = $mobile;
            $SmsLog->created_at = time();
            $SmsLog->endtime = 60 * 5 + time();
            $SmsLog->code = $code;
            $SmsLog->status = '1';
            $SmsLog->msg = '';
            $SmsLog->scene = $config['scene']['register']['no'];
            $SmsLog->save();
            return true;
        } else {
            $SmsLog->error_msg = $status;
            return false;
        }
    }

    // 验证验证码
    public static function validate($mobile, $tpl, $code)
    {
        $site_id = '1';
        $config = self::getconfig($site_id);
        $SmsLog = new SmsLog();
        $log = SmsLog::find()->where('mobile=:mobile and scene=:scene and code =:code ', [
            'mobile' => $mobile,
            'scene' => $config['scene'][$tpl]['no'],
            'code' => $code
        ])->orderBy('created_at DESC')->one();
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
                'code' => '2',
                'msg' => '验证失败'
            ];
        }
    }
}


