<?php
//微信配制
namespace backend\modules\wechat\service;

/**
 * DefaultController implements the CRUD actions for Category model.
 */
use Yii;
use backend\modules\admin\models\SiteWxconfig;
class siteconfig 
{
    public static function getSite($site_id)
    {
        return SiteWxconfig::find()->select(['site_id','appid','mch_id','type'])->where(['site_id'=>$site_id])->all();
    }
    public static function getCount($site_id,$type)
    {
        return SiteWxconfig::find()->where(['site_id'=>$site_id,'type'=>$type])->count();
    }
    public static function Create($site_id,$type,$appid,$appsecret,$mch_id,$partnerkey,$ssl_cer,$ssl_key)
    {
        $config = new SiteWxconfig();
        $config->site_id = $site_id;
        $config->type = $type;
        $config->appid = $appid;
        $config->appsecret = $appsecret;
        $config->mch_id = $mch_id;
        $config->partnerkey = $partnerkey;
        $config->ssl_cer = $ssl_cer;
        $config->ssl_key = $ssl_key;
        $config->cachepath = './'.$site_id.'/Cache';
        
        return $config->save();
    }
    public static function Update($id,$type,$appid,$appsecret,$mch_id,$partnerkey,$ssl_cer,$ssl_key)
    {
        $config = SiteWxconfig::findOne(['id'=>$id]);
        if(empty($config)){
            return null;
        }
        $config->type = $type;
        $config->appid = $appid;
        $config->appsecret = $appsecret;
        $config->mch_id = $mch_id;
        $config->partnerkey = $partnerkey;
        $config->ssl_cer = $ssl_cer;
        $config->ssl_key = $ssl_key;
        
        return $config->save();
    }
    public static function Delete($id,$site_id)
    {
        $config = SiteWxconfig::findOne(['id'=>$id,'site_id'=>$site_id]);
        if(empty($config)){
            return null;
        } 
        return $config->delete();
    }
}
