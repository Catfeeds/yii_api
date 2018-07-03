<?php
namespace h5\modules\v1\service;

use Yii;
use backend\modules\wechat\service\wechat;
use common\extensions\Wechat\WechatPay;

date_default_timezone_set('Asia/Shanghai');

class WeChatJsApiPay
{
    public static function checkArray($array =[])
    {
        if(empty($array['openid'])){
            throw "openid为空";
        }
        if(empty($array['order_sn']))
        {
            throw "订单号为空";
        }
        if(empty($array['price']))
        {
            throw "价格为空";
        }
        if(empty($array['notifyUrl']))
        {
            $array['notifyUrl'] = Yii::$app->params['wx_h5_pay']['notifyUrl'];
        }
        if(empty($array['body']))
        {
            $array['body'] = "小牛科技";
        }
        return $array;
    }
    /*
     * 微信网页支付
     */
    public static function pay($array =[],$site_id=2)
    {
        $array = static::checkArray($array);
        
        $wechatconfig = wechat::getconfig($site_id);
        
        $pay = new WechatPay($wechatconfig);
        $result = $pay->getPrepayId($array['openid'],$array['body'], $array['order_sn'],$array['price'],$array['notifyUrl'], $trade_type = "JSAPI");
        if($result===FALSE){
            return false;
        }else{
            $prepay_id = $result;
        }
        $options = $pay->createMchPay($prepay_id);
        return $options;
    }
}