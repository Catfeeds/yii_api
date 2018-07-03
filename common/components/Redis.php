<?php
namespace common\component;
use function yii\redis\set;

class Redis extends \yii\redis\Connection{
       public function seta($key,$value,$duration){
           \Yii::$app->redis->set($key, $value);
           \Yii::$app->redis->expire($key,$duration);
       }
}
