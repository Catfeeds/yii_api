<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\admin\service;

use Yii;
use backend\modules\admin\models\Site;
use backend\modules\admin\models\SiteConfig;

class SiteService
{
    
    public static function show_work($site_id)
    {
        return Site::find()->select(['site_id','name','on_work'])->where(['site_id'=>$site_id])->one();
    }
    public static function turn_work($site_id)
    {
        $site = Site::findOne(['site_id'=>$site_id]);
        $site->on_work = abs($site->on_work - 1);
        return $site->save();
    }
    
    public static function showmysite($user_id)
    {
       return Site::getMySite($user_id);
    }
    //获取site
    public static function showsite($user_id,$site_id)
    {
        return Site::getMySiteone($user_id, $site_id);
    }
    //修改店铺信息
    public static function updatesite($user_id,$site_id,$name='',$image='',$logo='',$date)
    {
        $site = Site::getMySiteone($user_id, $site_id);
        //审核中 和 过审的不许修改店铺信息
        if($site->state == Site::IN_VERYIFY || $site->state == Site::PASS_VERYIFY)
        {
            return false;
        }
        if(empty($site)){
            return null;
        }      
        if (empty($name)) $site->name = $name;
        if (empty($logo)) $site->logo = $logo;
        if (empty($image)) $site->image = $image;
		if($site -> load($date , '') && $site -> save()){
            return $site;
        } else {
            return null;
        }
    }
    //店铺续费支付
    public static function paythis($site_id,$price)
    {
        
    }
    //店铺续费价格计算
    public static function payprice($site_id,$time)
    {
        //计算价格
        //return 元
        
        return 0.01; 
    }
    //店铺延长使用时间
    public static function addexpires($site_id,$time)
    {
        //未过期 已有时间加时间
        $site = Site::findOne(['site_id'=>$site_id]);
        if($site->expires > time()){
            $site->expires += (int)$time;
        }else{//已过期 现在时间加时间
            $site->expires = time() + (int)$time;
        }
        return $site->save();
    }
    
    //设置起送价格和配送费
    public static function priceConfig($site_id,$shipping_price,$offer_price)
    {
        $siteconfig = SiteConfig::findOne(['site_id'=>$site_id]);
        if(empty($siteconfig)){
            $siteconfig = new SiteConfig();
            $siteconfig ->site_id = $site_id;
        }
        $siteconfig->shipping_price = $shipping_price;
        $siteconfig->offer_price = $offer_price;
        return $siteconfig->save();
    }
    //获取起送价格和配送费
    
    public static function Shipprice($site_id)
    {
        return SiteConfig::find()->select('site_id,shipping_price,offer_price')->where(['site_id'=>$site_id])->one();
    }

}


