<?php
//测试
//http://localhost/hoge/api/web/goods
namespace api\controllers;
use yii\rest\ActiveController;
class GoodController extends ActiveController
{
    public $modelClass = 'api\models\Goods';
//     public $modelClass = 'api\modules\v1\models\Member';
}