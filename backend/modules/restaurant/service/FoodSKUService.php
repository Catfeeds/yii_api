<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;
use backend\modules\restaurant\models\FoodSKU;
use backend\modules\restaurant\models\Food;

class FoodSKUService
{

    public static function showbyfood($food_id){
        return FoodSKU::showbyfood($food_id);
    }
    public static function showbyid($id){
        return FoodSKU::showbyid($id);
    }
    
    public static function create ($data)
    {
        $model = new FoodSKU();
        if(empty($data['store_count'])){
            $data['infinite_count'] == Food::IS_INFINITE_COUNT;
        }else if($data['infinite_count'] == Food::IS_INFINITE_COUNT){
            $data['store_count'] = 0;
        }
        //食品价格取sku中的最低值
        if(empty($data['price'])){
            return null;
        }
        $food = Food::findOne(['food_id'=>$data['food_id']]);
        $skuprice = FoodSKU::find('price')->where('food_id = '.$data['food_id'])->orderBy('price')->one();
        if($skuprice['price']<$data['price']){
            $lowprice = $skuprice['price'];
        }else{
            $lowprice = $data['price'];
        }
        if($food->price > $lowprice ||$food->price == 0){
            $food->sku = Food::HAVE_SKU;
            $food->price = $data['price'];
            $food->save();
        }else if($model->load($data,'')&&$model->validate()){
            if($model->save()){
                return $model;
            }
        }else{
            return null;
        }
    }
    
    public static function update($data)
    {
        $model = FoodSKU::showbyid($data['id']);
        if(!empty($model)&&$model->load($data,'')&&$model->validate()){
            if($model->save()){
                return $model;
            }
        }else{
            return null;
        }
    }
    
    public static function delete($food_id){
        $model = FoodSKU::showbyid($food_id);
        if(empty($model)){
            return false;
        }
        return $model->delete();
    }
}


