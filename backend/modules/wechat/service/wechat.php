<?php
// 微信配制
namespace backend\modules\wechat\service;

/**
 * DefaultController implements the CRUD actions for Category model.
 */
use Yii;
use backend\modules\admin\models\SiteWxconfig;

class wechat
{

    // 获取站的配制文件
    /*
     * 先从config表中取数据。无数据则取默认数据。
     * type === 0 为公众号配置
     * type === 1 为小程序配置
     */
    public static function getconfig($site_id = null, $type = '0')
    {
        $options = SiteWxconfig::find()->select([
            'appid',
            'appsecret',
            'mch_id',
            'partnerkey',
            'ssl_cer',
            'ssl_key',
            'cachepath'
        ])
            ->where([
            'site_id' => $site_id,
            'type' => $type
        ])
            ->asArray()
            ->one();
        if (empty($options) && $type == 0) {
            return array(
                'appid' => Yii::$app->params['wx_h5_pay']['appid'], // 填写高级调用功能的app id, 请在微信开发模式后台查询
                'appsecret' => Yii::$app->params['wx_h5_pay']['appsecret'], // 填写高级调用功能的密钥
                'token' => 'wechat', // 填写你设定的key
                'encodingaeskey' => '', // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
                'mch_id' => Yii::$app->params['wx_h5_pay']['mchid'], // 微信支付，商户ID（可选）
                'partnerkey' => Yii::$app->params['wx_h5_pay']['key'], // 微信支付，密钥（可选）
                'ssl_cer' => Yii::$app->params['wx_h5_pay']['ssl_cer'], // 微信支付，证书cert的路径（可选，操作退款或打款时必需）
                'ssl_key' => Yii::$app->params['wx_h5_pay']['ssl_key'], // 微信支付，证书key的路径（可选，操作退款或打款时必需）
                'cachepath' => '' // 设置SDK缓存目录（可选，默认位置在./src/Cache下，请保证写权限）
            );
        } elseif (empty($options) && $type == 1) {
            $options = array(
                'appid' => Yii::$app->params['wx_api_pay']['appid'], // 填写高级调用功能的app id, 请在微信开发模式后台查询
                'appsecret' => Yii::$app->params['wx_api_pay']['appsecret'], // 填写高级调用功能的密钥
                'token' => 'wechat', // 填写你设定的key
                'encodingaeskey' => '', // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
                'mch_id' => Yii::$app->params['wx_h5_pay']['mchid'], // 微信支付，商户ID（可选）
                'partnerkey' => Yii::$app->params['wx_h5_pay']['key'], // 微信支付，密钥（可选）
                'ssl_cer' => Yii::$app->params['wx_h5_pay']['ssl_cer'], // 微信支付，证书cert的路径（可选，操作退款或打款时必需）
                'ssl_key' => Yii::$app->params['wx_h5_pay']['ssl_key'], // 微信支付，证书key的路径（可选，操作退款或打款时必需）
                'cachepath' => '' // 设置SDK缓存目录（可选，默认位置在./src/Cache下，请保证写权限）
            );
        } else {
            $options['token'] = 'wechat';
            $options['encodingaeskey'] = '';
        }
        return $options;
    }
}
