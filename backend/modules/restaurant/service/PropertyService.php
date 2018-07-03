<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;
use backend\modules\restaurant\models\FoodProperty;
use backend\modules\restaurant\models\FoodPropertychild;

class PropertyService
{
    public static function showbyfood ($food_id)
    {
        return FoodProperty::showbyfoodid($food_id);
    }
    public static function showall($food_id)
    {
        $models = FoodProperty::showbyfoodid($food_id);
        $pros = array();
        foreach ($models as $model){
            array_push($pros,['property'=>$model,'property_child'=>FoodPropertychild::showbyproid($model['property_id'])]);
        }
        return $pros;
    }
    public static function showbyid($id)
    {
        $pro = FoodProperty::showproid($id); 
        $pro_child = FoodPropertychild::showbyproid($pro->property_id);
        return ['pro'=>$pro,'pro_child'=>$pro_child];
    }
    
    public static function createwith ($data)
    {
        $model = new FoodProperty();
        if($model->load($data,'')&&$model->validate()){
            return $model->save();
        }else{
            return false;
        }
    }
    
    public static function createwithchild ($pro,$prochild)
    {
        $model = new FoodProperty();
        if($model->load($pro,'')&&$model->validate()){
            $model->save();
            if(empty($model)){
                return false;
            }
            foreach ($prochild as $child){
                $models = new FoodPropertychild();
                $models->property_id = $model->property_id;
                $models->load($child,'');
                $models->save();
            }
            return $model;
        }else{
            return false;
        }
    }
    
    public static function updatewithchilde($pro,$prochild)
    {
        $model = FoodProperty::showproid($pro['property_id']);
        if(!empty($model)&&$model->load($pro,'')&&$model->validate()){
            $model->save();
            foreach ($prochild as $child)
            {
                if(empty($child['id'])){
                    $models = new FoodPropertychild();
                    $models->property_id = $model->property_id;
                }else{
                    $models = FoodPropertychild::showproid($child['id']);
                }
                if(empty($child['name'])){
                    $model->delete();
                }else{
                    $models->load($child,'');
                    $models->save();
                }
            }
            return $model;
        }else{
            return null;
        }
    }
    
    public static function update($data)
    {
        $model = FoodProperty::showproid($data['property_id']);
        if(!empty($model)&&$model->load($data,'')&&$model->validate()){
            if($model->save()){
                return $model;
            }
        }else{
            return null;
        }
    }
    
    public static function updatechild($data)
    {
        $model = FoodPropertychild::showproid($data['id']);
        if(!empty($model)&&$model->load($data,'')&&$model->validate()){
            if($model->save()){
                return $model;
            }
        }else{
            return null;
        }
    }
    
    public static function delete($id){
        $model = FoodProperty::showproid($id);
        if(empty($model)){
            return false;
        }
        $pro_id = $model->property_id;
        $child = FoodPropertychild::showbyproid($pro_id);
        if(!empty($child)){
            foreach ($child as $c){
                $c->delete();
            }
        }
        return $model->delete();
    }
    public static function deletechild($id){
        $model = FoodPropertychild::showproid($id);
        if(empty($model)){
            return false;
        }
        return $model->delete();
    }
}


