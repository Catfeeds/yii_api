<?php
/**
 * @date        : 2017年8月30日
 * @author      : Jason
 * @copyright   : http://www.ixn100.com/
 * @description : 后台用户服务
 */
namespace backend\modules\rabc\service;

use yii;

class RabcService
{

    public static function router()
    {
        $module = \Yii::$app->controller->module->id;
        $controller = \Yii::$app->controller->id;
        $action = \Yii::$app->controller->action->id;
        return "NO";
    }
}


