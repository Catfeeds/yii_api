<?php
/**
 * @date        : 2018年2月1日
 */
namespace backend\modules\sms\service;
ini_set("display_errors", "on");
use Yii;

require_once  Yii::getAlias('@aliyunsms'). '/api_sdk/vendor/autoload.php';
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use common\utils\StringUtil;
use backend\modules\sms\models\SmsLog;

// 加载区域结点配置
Config::load();

/*
 *短信独立 
 */
class SMSService
{
    static $acsClient = null;
    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";
        
        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";
        
        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = Yii::$app->params['aliyunsms']['accessKeyId']; // AccessKeyId
        
        $accessKeySecret = Yii::$app->params['aliyunsms']['accessKeySecret']; // AccessKeySecret
        
        // 暂时不支持多Region
        $region = "cn-hangzhou";
        
        // 服务结点
        $endPointName = "cn-hangzhou";
        
        
        if(static::$acsClient == null) {
            
            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
            
            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
            
            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }
    
    /**
     * 发送短信
     * @return stdClass
     */
    public static function sendSms() {
        
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        
        // 必填，设置短信接收号码
        $request->setPhoneNumbers("");
        
        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName(Yii::$app->params['aliyunsms']['sign']);
        
        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode(Yii::$app->params['aliyunsms']['template']);
        
        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
                "code"=>"12345",
                "product"=>"dsd"
        ), JSON_UNESCAPED_UNICODE));
        
        // 可选，设置流水号
        $request->setOutId("yourOutId");
        
        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        $request->setSmsUpExtendCode("1234567");
        
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);
        
        return $acsResponse;
        
    }
    
    /**
     * 短信发送记录查询
     * @return stdClass
     */
    public static function querySendDetails() {
        
        // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetailsRequest();
        
        // 必填，短信接收号码
        $request->setPhoneNumber("12345678901");
        
        // 必填，短信发送日期，格式Ymd，支持近30天记录查询
        $request->setSendDate("20170718");
        
        // 必填，分页大小
        $request->setPageSize(10);
        
        // 必填，当前页码
        $request->setCurrentPage(1);
        
        // 选填，短信发送流水号
        $request->setBizId("yourBizId");
        
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);
        
        return $acsResponse;
    }
    
    public static function sendSmswithnum($num,$status='') {
        
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        
        // 必填，设置短信接收号码
        $request->setPhoneNumbers($num);
        
        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName(Yii::$app->params['aliyunsms']['sign']);
        
        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode(Yii::$app->params['aliyunsms']['template']);
        
        $code = StringUtil::random(4);
        
        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
                "code"=>$code,
                "product"=>"dsd"
        ), JSON_UNESCAPED_UNICODE));
        
        // 可选，设置流水号
        $request->setOutId("yourOutId");
        
        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        $request->setSmsUpExtendCode("1234567");
        
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);
        
        $SmsLog = new SmsLog();
        if ($acsResponse) {
            $SmsLog->mobile = $num;
            $SmsLog->created_at = time();
            $SmsLog->endtime = 60 * 5 + time();
            $SmsLog->code = $code;
            $SmsLog->status = $status;
            $SmsLog->msg = '';
            $SmsLog->scene = '1';
            $SmsLog->save();
            return true;
        } else {
            $SmsLog->error_msg = $acsResponse;
            return false;
        }
        //return $acsResponse;
    }
    
    public static function validate($mobile,$status, $code)
    {
        //$site_id = '1';
        //$config = self::getconfig($site_id);
        $SmsLog = new SmsLog();
        $log = SmsLog::find()->where('mobile=:mobile and status=:status and code =:code ', [
                ':mobile' => $mobile,
                ':status' => $status,
                ':code' => $code
        ])->orderBy('created_at DESC')->one();
        
        if ($log) {
            if (time() < $log->endtime) {
                $log -> endtime = 0;
                $log ->save();
                return [
                        'code' => '0',
                        'msg' => '验证成功'
                ];
            } elseif (time() > $log->endtime) {
                $log -> endtime = 0;
                $log ->save();
                return [
                        'code' => '1',
                        'msg' => '验证码已过期'
                ];
            } else {
                $log -> endtime = 0;
                $log ->save();
                return [
                        'code' => '2',
                        'msg' => '验证码错误'.time()
                ];
            }
        } else {
            $log ->save();
            return [
                    'code' => '3',
                    'msg' => $log.'验证码错误'
            ];
        }
    }
}


