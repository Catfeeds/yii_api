<?php
/**
 * @date        : 2018年1月29日
 */
namespace api\modules\v1\service;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\ResCategory;
use backend\modules\restaurant\models\FoodSKU;
use backend\modules\restaurant\models\FoodProperty;

class FoodService
{
    public static function showbycat($site_id,$cat_id)
    {
        $models = Food::showcatidwithsale($site_id, $cat_id);
        foreach ($models as $i=>$model){
            $models[$i]['sku'] = FoodSKU::findAll(['food_id'=>$model['food_id']]);
            $models[$i]['pro'] = FoodProperty::showthis($model['food_id']);
        }
        return $models;
    }
    
    public static function showsite($site_id)
    {
        $models = Food::showallwithsale($site_id);
        foreach ($models as $i=>$model){
            $models[$i]['sku'] = FoodSKU::findAll(['food_id'=>$model['food_id']]);
            $models[$i]['pro'] = FoodProperty::showthis($model['food_id']);
        }
        return $models;
    }
    
    public static function showcat($site_id)
    {
        return ResCategory::findbysite($site_id);
    }
}


