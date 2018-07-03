<?php
namespace backend\modules\analysis\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\analysis\service\OrderService;

class OrderController extends BaseController
{

    // 当天营业额 //今日付款单数 //今日浏览量
    public function actionSales()
    {
        $site_id = $this->getSite();
        $model = OrderService::Salesday($site_id);
        return $this->jsonSuccess([
            'money' => $model['money'],
            'finishcount' => $model['finishcount'],
            'paycount' => $model['paycount'],
            'createcount' => $model['createcount'],
            'click' => $model['click']
        ], '今日营业额和付款单数');
    }

    //积压维权
    public function actionRefund()
    {
        $site_id = $this->getSite();
        $model = OrderService::refunds($site_id);
        return $this->jsonSuccess(['refundcount'=>$model],'积压维权');
    }
    
    public function actionSalesyesterday()
    {
        $site_id = $this->getSite();
        $week_time = date("Y-m-d",strtotime("-1 day"));
        $model = OrderService::Saleslast($site_id,$week_time);
        return $this->jsonSuccess([
            'money' => $model['money'],
            'finishcount' => $model['finishcount'],
            'paycount' => $model['paycount'],
            'createcount' => $model['createcount'],
            'click' => $model['click']
        ], '查询成功');
    }
    public function actionSalesweek()
    {
        $site_id = $this->getSite();
        $week_time = date('Y-m-d', strtotime("this week Monday", time()));
        $model = OrderService::Saleslast($site_id,$week_time);
        return $this->jsonSuccess([
            'money' => $model['money'],
            'finishcount' => $model['finishcount'],
            'paycount' => $model['paycount'],
            'createcount' => $model['createcount'],
            'click' => $model['click']
        ], '查询成功');
    }
    public function actionWeektrend()
    {
        $site_id = $this->getSite();
        $models = OrderService::Salestrend($site_id,7);
        return $this->jsonSuccess($models,'七日趋势图');
    }
    
    //获取之前销售情况
    public function actionGetold()
    {
        $site_id = $this->getSite();
        $time = Yii::$app->request->get('time');
        if(empty($time)){
            return $this->jsonFail([],'时间错误！');
        }
        $model = OrderService::Saleslast($site_id,$time);
        return $this->jsonSuccess([
            'money' => $model['money'],
            'finishcount' => $model['finishcount'],
            'paycount' => $model['paycount'],
            'createcount' => $model['createcount'],
            'click' => $model['click']
        ], '查询成功');
    }
    // 每日销售额写入
    public function actionSetsales()
    {
        return OrderService::Setsalesday();
    }
    // 销售情况补写
    public function actionSetsalesold()
    {
        $site_id = $this->getSite();
        $days = Yii::$app->request->get('days');
        return OrderService::Setsales($site_id,$days);
    }
}