<?php

/**
 * @author Jason
 * @date 2016-08-08
 * @copyright Copyright © 2016年 EleTeam
 */
namespace h5\modules\v1\controllers;

use Yii;
use h5\base\BaseController;
use backend\modules\restaurant\models\ResCategory;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\FoodSKU;
use backend\modules\restaurant\models\FoodProperty;
use backend\modules\admin\models\Site;
use backend\modules\admin\models\SiteConfig;

/**
 * 食品详情控制
 * Class ShowfoodController
 *
 * @package api\modules\v1\controllers
 */
class ShowfoodController extends BaseController
{
    public function actionShowsite()
    {
        $site_id = $this->getSite();
        $site = Site::find()->select(['name','description','created_at','on_work','logo'])->where(['site_id'=>$site_id])->asArray()->one();
        if(empty($site)){
            return $this->jsonFail('','未查询到店铺');
        }
        if(empty($site['logo'])){
            $site['logo'] = Yii::$app->params['logo'];
        }
        $array = SiteConfig::find()->select('shipping_price,offer_price,times')->where(['site_id'=>$site_id])->asArray()->one();
        if(empty($array)){
            $array=(['shipping_price'=>Yii::$app->params['site']['shipping_price'],'offer_price'=>Yii::$app->params['site']['offer_price'],'times'=>60*30]);
        }
        $site = array_merge($site,$array);
        if(empty($site)){
            return $this->jsonFail('','未查询到店铺');
        }else{
            return $this->jsonSuccess($site,'查询成功');
        }
    }
    public function actionShowcat()
    {
        $site_id = Yii::$app->request->get('site_id');
        $model = ResCategory::findbysite($site_id);
        if(empty($model)){
            return $this->jsonFail([],'查询失败');
        }else{
            return $this->jsonSuccess($model,'查询成功');
        }
       
    }
    public function actionView()
    {
        $food_id = Yii::$app->request->get('food_id');
        $food = Food::find()->where(['food_id'=>$food_id])->one();
        $food['sku'] = FoodSKU::findAll(['food_id'=>$food['food_id']]);
        $food['pro'] = FoodProperty::showthis($food['food_id']);
        if(empty($food)){
            return $this->jsonFail([],'未查询到');
        }else{
            $food['image'] = array_filter(explode(";",$food['image']));
            return $this->jsonSuccess($food,'查询成功');
        }
    }
    public function actionShowfood()
    {
        $site_id = Yii::$app->request->get('site_id');
        $cat_id = Yii::$app->request->get('cat_id');
        $models = Food::showcatidwithsale($site_id, $cat_id);
        foreach ($models as $i=>$model){
            $models[$i]['sku'] = FoodSKU::findAll(['food_id'=>$model['food_id']]);
            $models[$i]['pro'] = FoodProperty::showthis($model['food_id']);
        }
        if(empty($models)){
            return $this->jsonFail([],'未查询到');
        }else{
            foreach ($models as $i=>$model){
                $models[$i]['image'] = array_filter(explode(";",$model['image']));
            }
            return $this->jsonSuccess($models,'查询成功');
        }
    }
}
