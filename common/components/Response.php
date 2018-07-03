<?php
namespace common\component;
use yii;
class Response extends \yii\web\Response{
   /* public function Code($error_code) {
        $requests = Yii::$app->request; //返回值
        $error_file = require_once \Yii::$app->basePath . '/messages/'.Yii::$app->language.'/errorcode.php';
        $error_body = [
            'request' => $requests->getUrl(),
//             'method'=>$requests->getMethod(),
//             'name' => $error_code,
            'hoge' => '1',
        ];
        return $error_file["$error_code"] + $error_body;
    }

    public function alert($message, $code = 1, $data = null){
        $this->format = self::FORMAT_JSON;
        $this->data = [
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ];
        return $this;
    }*/

}
