<?php

/**
 * @author Jason
 * @date 2016-08-08
 * @copyright Copyright © 2016年 EleTeam
 */
namespace api\modules\v1\controllers;
use api\base\BaseController;
use Yii;
use api\modules\v1\service\FoodCarService;
use api\modules\v1\models\FoodCarNum;

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
        $table_id = Yii::$app->request->post('table_id');
        $food = Yii::$app->request->post('food');
        $site_id = $this->getSite();
        if (empty($food['num'])) {
            $num = 1;
        } else {
            $num = $food['num'];
        }
        
        $msg = FoodCarService::addcar($table_id, $user_id, $food['food_id'], $num,$food['sku'], $food['pro']);
        
        if ($msg == 'SUCCESS') {
            // 发送广播
            $cat = FoodCarNum::find()->where(['tableid'=>$table_id])->all();
            return $this->jsonSuccess(['num' => FoodCarService::getnum($table_id,$food['food_id']),'cat'=>$cat,'price'=>FoodCarService::theprice($table_id)], '成功');
        } else {
            return $this->jsonFail([],$msg);
        }
    }

    public function actionCardel ()
    {
        $user_id = $this->getUserId();
        $table_id = Yii::$app->request->post('table_id');
        $food = Yii::$app->request->post('food');
        $num = Yii::$app->request->post('num');
        if(empty($num)){
            $num = 1;
        }
        if (FoodCarService::deletecar($table_id, $user_id, $food['food_id'],$num, $food['sku'], $food['pro'])) {
            // 发送广播
            $cat = FoodCarNum::find()->where(['tableid'=>$table_id])->all();
            return $this->jsonSuccess(['num' => FoodCarService::getnum($table_id,$food['food_id']),'cat'=>$cat,'price'=>FoodCarService::theprice($table_id)], '成功');
        } else {
            return $this->jsonFail([], '失败');
        }
    }

    public function actionCarclear ()
    {
        $user_id = $this->getUserId();
        $table_id = Yii::$app->request->post('table_id');
        if (empty($table_id)) {
            return $this->jsonFail([], '失败');
        }
        if (FoodCarService::clearcar($table_id)) {
            // 关闭桌子
            return $this->jsonSuccess([], '成功');
        } else {
            return $this->jsonFail([], '失败');
        }
    }

    public function actionShowcar ()
    {
        $table_id = Yii::$app->request->post('table_id');
        if (! empty($model = FoodCarService::showall($table_id))) {
            return $this->jsonSuccess($model, '成功');
        } else {
            return $this->jsonFail([], '失败');
        }
    }
}
