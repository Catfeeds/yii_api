<?php
namespace api\modules\v1\controllers;
use Yii;
use api\base\BaseController;
use backend\modules\mall\models\Region;

class RegionController extends BaseController
{

    // 显示省级地区
    public function actionIndex ()
    {
        $regions = Region::find()->where('level = :level', 
                [
                        ':level' => Region::STATUS_PROVINCE
                ])->all();
        
        return $this->jsonSuccess($regions, '查询成功');
    }

    // 根据父id显示地区
    public function actionShowbypid ()
    {
        $pid = Yii::$app->request->get('pid');
        $regions = Region::find()->where('parent_id = :parent_id', 
                [
                        ':parent_id' => $pid
                ])->all();
        return $this->jsonSuccess($regions, '查询成功');
    }

    // 根据id显示具体地址名称
    public function actionShow ()
    {
        $id = Yii::$app->request->get('id');
        $region = Region::find()->where('id=:id', [
                ':id' => $id
        ])->one();
        return $this->jsonSuccess($region, '查询成功');
    }

    // 根据最低等级的id显示从省份到
    public function actionShowall ()
    {
        $id = Yii::$app->request->get('id');
        $regions = array();
        $region = Region::find()->where('id=:id', [
                ':id' => $id
        ])->one();
        array_push($regions, $region);
        while (($region->level) != 1) {
            $region = Region::find()->where('id=:id', 
                    [
                            ':id' => $region['parent_id']
                    ])->one();
            array_push($regions, $region);
        }
        return $this->jsonSuccess($regions, '查询成功');
    }
}
