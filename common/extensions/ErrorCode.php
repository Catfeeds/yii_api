<?php
namespace common\utils;
use yii;
class ErrorCode{
public static function Code($error_code) {
    $requests = Yii::$app->request; //返回值
    $error_file = require_once \Yii::$app->basePath . '/messages/'.Yii::$app->language.'/errorcode.php';
    $error_body = [
        'request' => $requests->getUrl(),
        'method'=>$requests->getMethod(),
        'name' => $error_code
    ];
    return $error_body + $error_file["$error_code"];
}

}