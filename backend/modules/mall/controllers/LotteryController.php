<?php
namespace backend\modules\mall\controllers;

class LotteryController extends \yii\web\Controller
{

    public function actionIndex ()
    {
        return $this->render('index');
    }
}
