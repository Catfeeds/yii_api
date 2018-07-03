<?php
/**
 * @date        : 2018年1月29日
 */
namespace super\modules\super\service;

use super\modules\super\models\Super;
use backend\modules\admin\models\Admin;
use backend\modules\admin\models\Site;
use super\modules\super\models\AdminNote;

class AdminService
{

    // 都要带分页
    // 显示所有用户
    public static function showuser($offset, $limit)
    {
        return Admin::find()
        ->select(Admin::tableName().'.id,'.Admin::tableName().'.username,'.Admin::tableName().'.mobile,'.Admin::tableName().'.status,'.Admin::tableName().'.created_at,'.AdminNote::tableName().'.id as note_id,'.AdminNote::tableName().'.name,'.AdminNote::tableName().'.note')
            ->leftJoin(AdminNote::tableName(),AdminNote::tableName().'.id = '.Admin::tableName().'.id')
            ->orderby(Admin::tableName().'.created_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->all();
    }

    // 计算数据
    public static function countuser()
    {
        return Admin::find()->count();
    }

    // 根据手机号码显示用户
    public static function showone($mobile)
    {
        return Admin::find()->select('username,mobile,status,created_at')
            ->leftJoin(AdminNote::tableName(),AdminNote::tableName().'.id = '.Admin::tableName().'.id')
            ->where(Admin::tableName().'mobile = :$mobile or'.AdminNote::tableName().'.mobile =:mobile',[':mobile'=>$mobile,':mobile'=>$mobile])
            ->one();
    }

    // 根据客户状态查询客户
    public static function showbystatus($status, $offset, $limit)
    {
        return Admin::find()
        ->select(Admin::tableName().'.id,'.Admin::tableName().'.username,'.Admin::tableName().'.mobile,'.Admin::tableName().'.status,'.Admin::tableName().'.created_at,'.AdminNote::tableName().'.id as note_id,'.AdminNote::tableName().'.name,'.AdminNote::tableName().'.note')
            ->leftJoin(AdminNote::tableName(),Admin::tableName().'.id = '.AdminNote::tableName().'.id')
            ->where([Admin::tableName().'.status' => $status])
            ->orderby(Admin::tableName().'.created_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->all();
    }

    public static function countbystatus($status)
    {
        return Admin::find()->where(['status' => $status])->count();
    }

    // 显示店铺已过期的客户
    public static function showexpires($offset, $limit)
    {
        return Admin::find()
        ->select(Admin::tableName().'.id,'.Admin::tableName() .'.username,' . Admin::tableName() . '.mobile,' . Admin::tableName() . '.status,' . Admin::tableName() . '.created_at,'. AdminNote::tableName() . '.id as note_id,'. AdminNote::tableName() . '.name,'. AdminNote::tableName() . '.note,'. Site::tableName() . '.site_id,'. Site::tableName() . '.user_id,'. Site::tableName() . '.name as site_name ,' . Site::tableName() . '.expires')
            ->leftJoin(Site::tableName(), 'pre_admin.id = ' . Site::tableName() . '.user_id')
            ->leftJoin(AdminNote::tableName(),AdminNote::tableName().'.id = '.Admin::tableName().'.id')
            ->where(['<', Site::tableName() . '.expires' , time()])
            ->orderby('pre_site.created_at DESC')
            ->offset ( $offset )
            ->limit($limit)
            ->asArray()
            ->all();
    }
    
    public static function showexpires2($offset, $limit)
    {
    	return Admin::find()
    	->select(Admin::tableName().'.id,'.Admin::tableName() .'.username,' . Admin::tableName() . '.mobile,' . Admin::tableName() . '.status,' . Admin::tableName() . '.created_at,'. AdminNote::tableName() . '.id as note_id,'. AdminNote::tableName() . '.name,'. AdminNote::tableName() . '.note,'. Site::tableName() . '.site_id,'. Site::tableName() . '.user_id,'. Site::tableName() . '.name as site_name ,' . Site::tableName() . '.expires')
    	->leftJoin(Site::tableName(), 'pre_admin.id = ' . Site::tableName() . '.user_id')
    	->leftJoin(AdminNote::tableName(),AdminNote::tableName().'.id = '.Admin::tableName().'.id')
    	->where(['>=', Site::tableName() . '.expires' , time()])
    	->orderby('pre_site.created_at DESC')
    	->offset ( $offset )
    	->limit($limit)
    	->asArray()
    	->all();
    }
    
    public static function countexpires()
    {
    	return Admin::find()->leftJoin(Site::tableName(), 'pre_admin.id = ' . Site::tableName() . '.user_id') ->where(['<', Site::tableName() . '.expires' , time()])->count();
    }
    public static function countexpires2()
    {
    	return Admin::find()->leftJoin(Site::tableName(), 'pre_admin.id = ' . Site::tableName() . '.user_id') ->where(['>', Site::tableName() . '.expires' , time()])->count();
    }
    // 修改客户状态
    public static function updatestatus($id, $status)
    {
        $admin = Admin::findOne(['id' => $id]);
        $admin->status = $status;
        return $admin->save();
    }
    //修改用户备注信息
    public static function updateadminnote($super_id,$data)
    {
        $admin_note = AdminNote::findOne(['id'=>$data['id']]);
        if(empty($admin_note)){
            $admin_note = new AdminNote();
            $admin_note ->id = $data['id'];
        }
        $admin_note->load($data,'');
        $admin_note->super_id = $super_id;
        return $admin_note->save();
    }
}


