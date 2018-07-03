<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;
use backend\modules\restaurant\models\Tables;
use backend\modules\attachment\service\QrService;

class TablesService
{
    public static function setpeople($table_id,$people)
    {
        $table = Tables::findOne(['id'=>$table_id]);
        $table->people = $people;
        return $table->save();
    }
    public static function getpeople($table_id,$people)
    {
        return Tables::findOne(['id'=>$table_id])->people;
    }
    public static function showbysite ($site_id)
    {
        $model = Tables::findbysite($site_id);
        return $model;
    }
    public static function getname($id)
    {
        $model = Tables::findOne(['id'=>$id]);
        return $model->name;
    }
    // 修改桌位信息
    public static function update ($data)
    {
        $model = Tables::findbytableid($data['table_id'], $data['site_id']);
        
        if (! empty($model)) {
            if(!empty($data['name'])){
                $model -> name = $data['name'];
                $model -> QR_code = null;
            }
            if(!empty($data['chair'])){
                $model -> chair= $data['chair'];
            }
            if(!empty($data['status'])){
                $model -> status= $data['status'];
            }
            if ($model->save()) {
                return $model;
            }
        }
        return false;
    }

    // 添加桌位
    public static function create ($data)
    {
        $model = new Tables();
        
        $model->table_id = Tables::getThetableid($data['site_id']);
        if (empty($data['table_id'])) {
            $data['table_id'] = $model->table_id;
        }
        if ($model->load($data, '') && $model->validate()) {
            if ($model->save()) {
                return $model;
            }
        }
        return false;
    }

    // 删除分类
    public static function delete ($table_id, $site_id)
    {
        $model = Tables::findbytableid($table_id, $site_id);
        if (empty($model)) {
            return false;
        }
        return $model->delete();
    }
    // 生成单个二维码
    public static function createOneQr ($table)
    {
        if(empty($table)){
            return null;
        }
        $model = QrService::gettableQr($table);
        if(empty($model)){
            return null;
        }else{
            return $model;
        }
    }
    // 生成多个二维码
    public static function createQr ($tables)
    {
        if(empty($tables)){
            return null;
        }
        $model = QrService::gettableQrzip($tables);
        if(empty($model)){
            return null;
        }else{
            return $model;
        }
    }
}
