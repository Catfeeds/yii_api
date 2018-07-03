<?php
//分类管理
namespace backend\modules\mall\service;

use backend\modules\mall\models\Goods;

class Goods_service extends \yii\db\ActiveRecord
{
    public static function get_child_one_api($myid)
    {
    	return Goods::find()->where(['goods_id' => $myid])->one();
    }
}