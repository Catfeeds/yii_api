<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\admin\service;
use Yii;
use backend\modules\admin\models\Admin;
use backend\modules\admin\models\Site;
use backend\modules\restaurant\models\ResCategory;
use backend\modules\attachment\models\AttachmentCategory;
use super\modules\super\models\AdminNote;

class AdminService
{

    public static function findmobile ($mobile)
    {
        return Admin::findOne([
                'mobile' => $mobile
        ]);
    }

    public static function addadmin ($mobile,$password)
    {
        $model = new Admin();
        if(empty($password)||empty($mobile)){
            return null;
        }
        $mobile .= "";
        $model->mobile = $mobile;
        $model->username = '小牛用户'.$mobile;
        $model->password_hash = Yii::$app->security->generatePasswordHash($password);
        $model->generateAccessToken();
        $model->created_at = time();
        $model->updated_at = time();
        $model->status = Admin::STATUS_NOT_CALL;
        
        if ($model->validate()) {
            if ($model->save()) {
                //注册进来的时候添加一个供超管使用的用户信息
                $admin_note = new AdminNote();
                $admin_note->id = $model->id;
                $admin_note->save();
                
                $model = Admin::findByMobile($mobile);
                $site = static::createresandfoodcat($model->id);
                static::createpicdefaultcat($site->site_id);
                return ['access_token'=>$model->access_token,'site'=>[$site]];
            }
        }
        return null;     
    }
    public static function updatepassword($mobile,$password)
    {
        $model = Admin::findByMobile($mobile);
        if(empty($password)){
            return null;
        }
        $model->password_hash = Yii::$app->security->generatePasswordHash($password);
        $model->updated_at = time();
        if ($model->validate()) {
            if ($model->save()) {
                return $model->updated_at;
            }
        }
        
        return null;  
    }
    public static function adminlogin($mobile,$password)
    {
        $model = new Admin();
        $model->mobile = $mobile;
        $model->validate();
        $model = Admin::findByMobile($mobile);
        if(empty($model)){
            return 'no_mobile';
        }
        if(Yii::$app->security->validatePassword($password,$model['password_hash'])){
            $model->generateAccessToken();
            if($model->save()){
                return $model;
            }
        }
        return null;
    }
    public static function createresandfoodcat($user_id)
    {
        $site = new Site();
        $site->user_id = $user_id;
        $site->site_url = '/restaurant/'.$user_id;
        $site->created_at = time();
        $site->expires = time()+86400;
        $site->name = '小牛'.$user_id;
        $site->description = '餐饮';
        $site->save();
        
        $titles = array("主食","凉菜","热菜","饮料");
        foreach ($titles as $title){
            $cat = new ResCategory();
            $cat->name = $title;
            $cat->site_id = $site->site_id;
            $cat->order_num = 50;
            $cat->category_count = 0;
            $cat->save();
        }
        if(empty($site)){
            return null;
        }
        return $site;
    }
    public static function createpicdefaultcat($site_id)
    {
        $cateogyr = new AttachmentCategory();
        $cateogyr->site_id = $site_id;
        $cateogyr->catid = 0;
        $cateogyr->name = '默认分类';
        $cateogyr->last_update = time();
        $cateogyr->save();
    }
}


