<?php

/**
 * @author Jason
 * @date 2016-08-08
 * @copyright Copyright © 2016年 EleTeam
 */
namespace api\modules\v1\controllers;
use api\base\BaseController;
use Yii;
use api\modules\v1\service\FoodService;

/**
 * 食品控制
 * Class ShowfoodController
 *
 * @package api\modules\v1\controllers
 */
class ShowfoodController extends BaseController
{
    public function actionShowcat()
    {
        $site_id = Yii::$app->request->get('site_id');
        $model = FoodService::showcat($site_id);
        if(empty($model)){
            return $this->jsonFail([],'查询失败');
        }else{
            return $this->jsonSuccess($model,'查询成功');
        }
       
    }
    public function actionShowsite()
    {
        $site_id = Yii::$app->request->get('site_id');
        $model = FoodService::showsite($site_id);
        if(empty($model)){
            return $this->jsonFail([],'查询失败');
        }else{
            foreach ($model as $i=>$models){
                $model[$i]['image'] = array_filter(explode(";",$models['image']));
            }
            return $this->jsonSuccess($model,'查询成功');
        }
        
    }

    public function actionShowfood()
    {
        $site_id = Yii::$app->request->get('site_id');
        $cat_id = Yii::$app->request->get('cat_id');
        $model = FoodService::showbycat($site_id, $cat_id);
        if(empty($model)){
            return $this->jsonFail([],'未查询到');
        }else{
            foreach ($model as $i=>$models){
                $model[$i]['image'] = array_filter(explode(";",$models['image']));
            }
            return $this->jsonSuccess($model,'查询成功');
        }
    }
}
