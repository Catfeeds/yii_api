<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\FoodSKU;
use api\modules\v1\models\FoodCar;

class FoodService
{

    public static function showbysite ($site_id)
    {
        $models = Food::showall($site_id);
        foreach ($models as $i=>$model)
        {
            $models[$i]['image'] = array_filter(explode(";",$model->image));
        }
        return $models;
    }

    public static function showbycatid ($site_id, $cat_id)
    {
        $models = Food::showcatid($site_id, $cat_id);
        foreach ($models as $i=>$model)
        {
            $models[$i]['image'] = array_filter(explode(";",$model->image));
        }
        return $models;
    }
    public static function showsitewithsale($site_id,$sale){
        $models = Food::showallwithsale($site_id,$sale);
        foreach ($models as $i=>$model)
        {
            $models[$i]['images'] = array_filter(explode(";",$model->image));
            
        }
        return $models;
    }
    public static function showbycatidwithsale($site_id, $cat_id,$sale)
    {
        $models = Food::showcatidwithsale($site_id, $cat_id,$sale);
        foreach ($models as $i=>$model)
        {
            $models[$i]['images'] = explode(";",$model->image);
        }
        return $models;
    }
    public static function create ($data,$site_id)
    {
        $model = new Food();
        $images = $data['the_image'];
        if(empty($images)){
            return null;
        }
        $image = '';
        foreach ($images as $img)
        {
            $image .= $img .";";
        }
        $model->image=$image;
        $model->site_id = $site_id;
        if(empty($data['store_count'])){
            $data['infinite_count'] == Food::IS_INFINITE_COUNT;
        }
        if($data['infinite_count'] == Food::IS_INFINITE_COUNT){
            $data['store_count'] = 0;
        }
        if($model->load($data,'')&&$model->validate()){
            if($model->save()){
                CategoryService::foodcount($data['cat_id'],1);
                return $model;
            }
        }else{
            return null;
        }
    }
    
    public static function update($data)
    {
        $model = Food::findOne(['food_id'=>$data['food_id']]);
        $images = $data['the_image'];
        if(!empty($images)){
            $image = '';
            foreach ($images as $img)
            {
                $image .= $img .";";
            }
            $model->image = $image;
        }
        if(!empty($model)&&$model->load($data,'')&&$model->validate()){
            if($model->save()){
                return $model;
            }
        }else{
            return null;
        }
    }
    
    public static function delete($food_id){
        $model = Food::findOne(['food_id'=>$food_id]);
        if(empty($model)){
            return false;
        }
        $food_id = $model->food_id;
        
        FoodSKUService::delete($food_id);
        PropertyService::delete($food_id);
        CategoryService::foodcount($model->cat_id,-1);
        $model->is_del = Food::IS_DEL;
        return $model->save();
    }
    
    public static function saleornot($food_id,$sale)
    {
        $model = Food::findOne(['food_id'=>$food_id]);
        if(empty($model)){
            return false;
        }
        if($sale == Food::IS_ON_SALE)
        {
            $model->is_on_sale = Food::IS_ON_SALE;
            return $model->save();
        }else if($sale == Food::NOT_ON_SALE)
        {
            $model->is_on_sale = Food::NOT_ON_SALE;
            return $model->save();
        }
        return false;
    }
    
    public static function showthis($food_id)
    {
        $model = Food::findOne(['food_id'=>$food_id]);
        if(empty($model)){
            return null;
        }
        $model->image = array_filter(explode(";",$model->image));
        $sku = FoodSKU::showbyfood($food_id);
        $pro = PropertyService::showall($food_id);
        return ['food'=>$model,'sku'=>$sku,'pro'=>$pro];
    }
}


