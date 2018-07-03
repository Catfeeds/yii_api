<?php

namespace h5\modules\v1\controllers;
use h5\base\BaseController;
use Yii;
use api\modules\v1\models\FoodCar;
use backend\modules\restaurant\models\Food;
use api\modules\v1\models\FoodCarNum;
use backend\modules\restaurant\models\FoodSKU;
use h5\modules\v1\models\UserSite;

/**
 * 购物车
 * Class OrderController
 *
 * @package api\modules\v1\controllers
 */
class FoodcarController extends BaseController
{

    public function actionCaradd ()
    {
        $user_id = $this->getUserId();
        $site_id = $this->getSite();
        $food = Yii::$app->request->post('food');
        if(empty($food)){
            return $this->jsonFail([],'参数不完整');
        }
        $site_id = $this->getSite();
        if (empty($food['num'])) {
            $num = 1;
        } else {
            $num = $food['num'];
        }
        
        $foods = Food::findOne(['food_id'=>$food['food_id'],'site_id'=>$site_id,'is_del'=>Food::NOT_DEL]);
        if(empty($foods)){
            return $this->jsonFail([],'未查询到商品');
        }
        $price = $foods->price;
        $box_price = $foods->box_price;
        if(!empty($food['sku'])){
            $foodsku = FoodSKU::findOne(['food_id'=>$food['food_id'],'id'=>$food['sku']]);
            if(!empty($foodsku)){
                $price = $foodsku->price;
                $box_price = $foodsku->box_price;
                if($foodsku->infinite_count == 0 && $foodsku->store_count < $num){
                    return $this->jsonFail([],'库存不足');
                }
            }else{
            	$food['sku'] = null;
            }
        }
        if($foods->infinite_count == 0 && $foods->store_count < $num){
            return $this->jsonFail([],'库存不足');
        }
        
        $foodcar = FoodCar::find()->where(['user_id'=>$user_id,'food_id'=>$food['food_id'],'site_id'=>$site_id,'status'=>FoodCar::STATUS_H5])->andFilterWhere(['sku'=>$food['sku']])->one();
        
        if(empty($foodcar)){
            $foodcar = new FoodCar();
            $foodcar ->food_id = $food['food_id'];
            $foodcar ->site_id = $site_id;
            $foodcar ->user_id = $user_id;
            $foodcar ->table_id = 0;
            $foodcar ->sku = $food['sku'];
            $foodcar ->cat_id = $foods->cat_id;
            $foodcar ->status = FoodCar::STATUS_H5;
            $foodcar ->create_at = time();
            $foodcar ->number = $num;
        }else{
            $foodcar->number += $num;
        }
        $cat = FoodCarNum::findOne(['user_id'=>$user_id,'catid'=>$foods->cat_id]);
        if(empty($cat)){
            $cat = new FoodCarNum();
            $cat->site_id = $site_id;
            $cat->tableid = 0;
            $cat->user_id = $user_id;
            $cat->catid = $foods->cat_id;
            $cat->status = FoodCarNum::STATUS_H5;
            $cat->num = $num;
        }else{
            $cat->num += $num;
        }
        $cat->save();
        $cats = FoodCarNum::findAll(['user_id'=>$user_id,'site_id'=>$site_id,'status'=>FoodCarNum::STATUS_H5]);
        $user_site = UserSite::findOne(['user_id'=>$user_id,'site_id'=>$site_id]);
        if(empty($user_site)){
            $user_site = new UserSite();
            $user_site->user_id = $user_id;
            $user_site->site_id = $site_id;
        }
        $user_site->money += $price*$num;
        $user_site->box_price += $box_price*$num;
        
        $user_site->save();
        if ($foodcar->save()){
            return $this->jsonSuccess(['cats'=>$cats,'price'=>$user_site->money,'box_price'=>$user_site->box_price],'添加购物车成功');
        } else {
            return $this->jsonFail([],'添加购物车失败');
        }
    }

    public function actionCardel ()
    {
        $user_id = $this->getUserId();
        $food = Yii::$app->request->post('food');
        if(empty($food)){
            return $this->jsonFail([],'参数不完整');
        }
        $site_id = $this->getSite();
        if (empty($food['num'])) {
            $num = 1;
        } else {
            $num = $food['num'];
        }
        $foods = Food::findOne(['food_id'=>$food['food_id']]);
        $price = $foods->price;
        $box_price = $foods->box_price;
        if(!empty($food['sku'])){
            $foodsku = FoodSKU::findOne(['food_id'=>$food['food_id'],'id'=>$food['sku']]);
            if(!empty($foodsku)){
                $box_price = $foodsku->box_price;
                $price = $foodsku->price;
            }
        }
        $foodcar = FoodCar::find()->where(['user_id'=>$user_id,'food_id'=>$food['food_id'],'site_id'=>$site_id,'status'=>FoodCar::STATUS_H5])->andFilterWhere(['sku'=>$food['sku']])->one();
        if(empty($foodcar)){
            return $this->jsonFail([],'购物车无此商品');
        }
        if($foodcar->number > $num){
            $foodcar->number -= $num;
            $foodcar->save();
        }else{
            $foodcar->delete();
        }
        $cat = FoodCarNum::findOne(['user_id'=>$user_id,'catid'=>$foods->cat_id]);
        if(!empty($cat)){
            if($cat->num > $num)
            {
                $cat->num -= $num;
                $cat->save();
            }else 
            {
                $cat->delete();   
            }
        }
        
        $user_site = UserSite::findOne(['user_id'=>$user_id,'site_id'=>$site_id]);
        
        if(!empty($user_site)){
            $user_site->money -= $price*$num;
            $user_site->box_price -= $box_price*$num;
            $price = $user_site->money;
            $box_price = $user_site->box_price;
            if($user_site->money>0){
                $user_site->save();
            }else{
                $price = 0;
                $box_price = 0;
                $user_site->delete();
            }
        }
        
        $cats = FoodCarNum::findAll(['user_id'=>$user_id,'site_id'=>$site_id,'status'=>FoodCarNum::STATUS_H5]);
        return $this->jsonSuccess(['cats'=>$cats,'price'=>$price,'box_price'=>$box_price],'从购物车删除成功');
        
    }

    public function actionCarclear ()
    {
        $user_id = $this->getUserId();
        $site_id = $this->getSite();

        FoodCarNum::deleteAll(['user_id'=>$user_id,'site_id'=>$site_id,'status'=>FoodCarNum::STATUS_H5]);
        UserSite::deleteAll(['user_id'=>$user_id,'site_id'=>$site_id]);
        if (FoodCar::deleteAll(['user_id'=>$user_id,'site_id'=>$site_id,'status'=>FoodCarNum::STATUS_H5])) {
            return $this->jsonSuccess([], '成功');
        } else {
            return $this->jsonFail([], '失败');
        }
    }

    public function actionShowcar ()
    {
        $user_id = $this->getUserId();
        $site_id = $this->getSite();
        $cats = FoodCarNum::findAll(['user_id'=>$user_id,'site_id'=>$site_id,'status'=>FoodCarNum::STATUS_H5]);
        $user_site = UserSite::findOne(['user_id'=>$user_id,'site_id'=>$site_id]);
        if(empty($user_site)){
            $price = 0;
            $box_price = 0;
        }else{
            $price = $user_site->money;
            $box_price = $user_site->box_price;
        }
        
        $foodcar = FoodCar::find()->where(['user_id'=>$user_id,FoodCar::tableName().'.site_id'=>$site_id,'status'=>FoodCarNum::STATUS_H5])
                ->joinWith('food')
                ->joinWith('sku')
                ->asArray()
                ->all();
        
        if (! empty($foodcar)) {
            return $this->jsonSuccess(['cats'=>$cats,'price'=>$price,'box_price'=>$box_price,'foodcar'=>$foodcar], '成功');
        } else {
            return $this->jsonFail([], '未查询到');
        }
    }
}
