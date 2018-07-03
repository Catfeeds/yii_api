<?php
namespace backend\modules\wechat\service;

use backend\modules\wechat\models\WeixinMenu;
use common\extensions\Wechat\WechatMenu;
use backend\modules\wechat\models\WeixinMaterialFile;

/**
 * 微信菜单管理管理
 */
class MenuService
{
    public static function GetbySite($site_id)
    {
        $data = array(
            'button'=>array()
        );
        $buttons = WeixinMenu::find()->where(['siteid'=>$site_id,'parentid'=>0])->orderBy('sort')->asArray()->all();
        foreach ($buttons as $i => $button)
        {
            $list = WeixinMenu::find()->where(['siteid'=>$site_id,'parentid'=>$button['id']])->orderBy('sort')->asArray()->all();
            if(!empty($list)){
                $data['button'][$i]['id'] = $button['id'];
                $data['button'][$i]['sort'] = $button['sort'];
                $data['button'][$i]['name'] = $button['name'];
                $data['button'][$i]['sub_button'] = $list;
            }else{
                $data['button'][$i] = $button;
            }
        }
        return $data;
    }
    
    public static function Create($site_id,$data,$parentid=0)
    {
        if($parentid == 0){
            if(WeixinMenu::find()->where(['siteid'=>$site_id,'parentid'=>0])->count() >= 3){
                return '一级菜单已创建三个！';
            }
        }else{
            if(WeixinMenu::find()->where(['siteid'=>$site_id,'parentid'=>$parentid])->count() >= 5){
                return '二级菜单已创建五个！';
            }
        }
        $themenu = WeixinMenu::find()->select('sort')->where(['siteid'=>$site_id])->orderBy('sort')->one();
        if(empty($sort)){
            $sort = 1;
        }else{
            $sort = $themenu['sort']+1;
        }
        $menu = new WeixinMenu();
        $menu->name = $data['name'];
        $menu->parentid = $data['parentid'];
        $menu->sort = $sort;
        $menu->siteid = $site_id;
        $menu->type = $data['type'];
        if(!empty($data['url'])) $menu->url = $data['url'];
        if(!empty($data['media_id'])) $menu->media_id = $data['media_id'];
        
        if($menu->save()){
            return 'SUCCESS';
        }else{
            return '创建失败';
        }
    }
    public static function Update($site_id,$data)
    {
        $menu = WeixinMenu::findOne(['siteid'=>$site_id,'id'=>$data['id']]);
        if(empty($menu)){
            return '未查询到!';
        }
        if($menu->parentid == 0&&$data['parentid']!=0){
            $menu = WeixinMenu::findOne(['siteid'=>$site_id,'parentid'=>$data['id']]);
            if(empty($menu)){
                return '拥有子菜单的一级菜单无法修改为二级菜单！';
            }
        }
        
        $menu->name = $data['name'];
        $menu->parentid = $data['parentid'];
        $menu->type = $data['type'];
        if(!empty($data['url'])) $menu->url = $data['url'];
        if(!empty($data['media_id'])) $menu->media_id = $data['media_id'];
        
        if($menu->save()){
            return 'SUCCESS';
        }else{
            return '创建失败';
        }
    }
    public static function Del($site_id,$id)
    {
        $menu = WeixinMenu::findOne(['siteid'=>$site_id,'id'=>$id]);
        if(empty($menu)){
            return '未查询到!';
        }
        if($menu->parentid == 0&&$id!=0){
            WeixinMenu::deleteAll(['siteid'=>$site_id,'parentid'=>$id]);
        }
        if($menu->delete()){
            return 'SUCCESS';
        }else{
            return '创建失败';
        }
    }
    public static function Submit($site_id)
    {
        $data = array(
            'button'=>array()
        );
        $buttons = WeixinMenu::find()->where(['siteid'=>$site_id,'parentid'=>0])->orderBy('sort')->asArray()->all();
        foreach ($buttons as $i => $button)
        {
            $list = WeixinMenu::find()->where(['siteid'=>$site_id,'parentid'=>$button['id']])->orderBy('sort')->asArray()->all();
            if(!empty($list)){
                $data['button'][$i]['name'] = $button['name'];
                $data['button'][$i]['sub_button'] = $list;
            }else{
                $data['button'][$i] = $button;
            }
        }
        $menu = new WechatMenu($site_id);
        $msg = $menu->createMenu($data);
        if($msg){
            return $msg;
        }else{
            return false;
        }
    }
    
    public static function Sort($site_id,$sort_list)
    {
        foreach ($sort_list as $i => $sort_out){
            $model = WeixinMenu::findOne(['id'=>$sort_out['id'],'siteid'=>$site_id]);
            $model->sort = $i+1;
            $model->save();
            if(!empty($sort_out['child'])){
                foreach ($sort_out['child'] as $i => $child){
                    $model = WeixinMenu::findOne(['id'=>$child,'siteid'=>$site_id]);
                    $model->sort = $i+1;
                    $model->save();
                }
            }
        }
        return true;
    }
    
}