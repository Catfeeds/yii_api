<?php
//测试
namespace api\controllers;
use yii\rest\ActiveController;

class SsoController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Member';
    
}
