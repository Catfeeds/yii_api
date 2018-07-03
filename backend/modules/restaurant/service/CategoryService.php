<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;

use backend\modules\restaurant\models\ResCategory;

class CategoryService
{
    
    public static function showbysite($site_id){
        $model = ResCategory::findbysite($site_id);
        return $model;
    }
    public static function update($data){
        $model = ResCategory::findbyid($data['cat_id']);
        if(!empty($model)&&$model->load($data,'')&&$model->validate()){
            if($model->save()){
                return $model;        
            }
        }
        return null;   
    }
    //添加分类
    public static function create($data)
    {
        $model = new ResCategory();
        if($model->load($data,'')&&$model->validate()){
            if($model->save()){
                return $model;
            }
        }
        return null;
    }
    //删除分类
    public static function delete($cat_id)
    {
        $model = ResCategory::findbyid($cat_id);
        if(empty($model)){
            return false;
        }
        return $model->delete();
    }
    //置顶
    public static function totop($catid){
        $model = ResCategory::findbyid($catid);
        if(empty($model)){
            return false;
        }
        $site_id = $model->site_id;
        ResCategory::updateAll(['cat_top'=>0],['site_id'=>$site_id]);
        $model->cat_top = 1;
        return $model->save();
    }
    //分类菜品数量
    public static function foodcount($catid,$num)
    {
        ResCategory::updateAllCounters(['category_count' => $num], ['cat_id' => $catid]);
    }
}


