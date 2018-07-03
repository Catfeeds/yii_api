<?php
namespace backend\modules\analysis\service;

use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\SalesDays;

class OrderService
{
    //趋势图
    public static function Salestrend($site_id,$days)
    {
        $array = [];
        while ($days > 0){
            
            $time = date("Y-m-d",strtotime("-".$days." day"));
            
            
            $sum = SalesDays::find()->select(['sales','order_count'])->where([
                'site_id' => $site_id
            ])
            ->andWhere(['days'=>$time])
            ->one(); 
            
            if (empty($sum->sales)) {
                $sales = '0.00';
            } else {
                $sales = $sum->sales;
            }
            if (empty($sum->order_count)) {
                $finishcount = '0';
            } else {
                $finishcount = $sum->order_count;
            }
            $array[$time] = ['sales'=>$sales,'finishcount'=>$finishcount,'click'=>0];
            $days --;
        }
        return $array;
        
    }
    // 当天营业额 //今日付款单数 //今日浏览量
    public static function Salesday($site_id)
    {
        $time = strtotime(date("Y-m-d"), time());
        //当天营业额
        $sum = FoodOrder::find()->select([
            'sum(order_price) as sumMoney'
        ])
        ->where([
            'site_id' => $site_id
        ])
        ->andWhere([
            'order_status' => FoodOrder::ORDER_STATUS_FINISH
        ])
        ->andWhere('create_at >' . $time)
        ->one();
        
        if (empty($sum->sumMoney)) {
            $money = '0.00';
        } else {
            $money = $sum->sumMoney;
        }
        
        
        //当天完成订单
        $finishcount = FoodOrder::find()->where([
            'site_id' => $site_id
        ])
        ->andWhere([
            'order_status' => FoodOrder::ORDER_STATUS_FINISH
        ])
        ->andWhere('create_at >' . $time)
        ->count();
        
        //当天已付款未发货订单
        $paycount = FoodOrder::find()->where([
            'site_id' => $site_id
        ])
        ->andWhere([
            'order_status' => FoodOrder::ORDER_STATUS_PAY
        ])
        ->andWhere('create_at >' . $time)
        ->count();
        
        
        //当天未付款订单
        $creatcount = FoodOrder::find()->where([
            'site_id' => $site_id
        ])
        ->andWhere([
            'order_status' => FoodOrder::ORDER_STATUS_CREATE
        ])
        ->andWhere('create_at >' . $time)
        ->count();
        
        return ['money'=>$money,'finishcount'=>$finishcount,'paycount'=>$paycount,'createcount'=>$creatcount,'click'=>'0'];
    }

    //维权数量
    public static function refunds($site_id)
    {
        return FoodOrder::find()->where([
            'site_id' => $site_id
        ])
        ->andFilterWhere([
            'order_status' => FoodOrder::ORDER_STATUS_REDUNDS
        ])
        ->count();
    }

    /*
     * 销售额     * 下单笔数      * 待付款笔数     * 代发货笔数
     * 查询今日之前的对比信息
     */
    public static function Saleslast($site_id,$time)
    {
        $sum = SalesDays::find()->select([
            'sum(sales) as sumMoney',
            'sum(order_count) as sumCount'
        ])->where([
            'site_id' => $site_id
        ])->andWhere(['days'=>$time])
        ->one();
        
        if (empty($sum->sumMoney)) {
            $money = '0.00';
        } else {
            $money = $sum->sumMoney;
        }
        if (empty($sum->sumCount)) {
            $finishcount = '0';
        } else {
            $finishcount = $sum->sumCount;
        }
        $time = strtotime(date("Y-m-d"), time());
        //已付款未发货订单
        $paycount = FoodOrder::find()->where([
            'site_id' => $site_id
        ])
        ->andWhere([
            'order_status' => FoodOrder::ORDER_STATUS_PAY
        ])
        ->andWhere('create_at >' . $time)
        ->count();
        
        
        //未付款订单
        $creatcount = FoodOrder::find()->where([
            'site_id' => $site_id
        ])
        ->andWhere([
            'order_status' => FoodOrder::ORDER_STATUS_CREATE
        ])
        ->andWhere('create_at >' . $time)
        ->count();
        
        return ['money'=>$money,'finishcount'=>$finishcount,'paycount'=>$paycount,'createcount'=>$creatcount,'click'=>0];
    }

    // 每日销售额写入
    public static function Setsalesday()
    {
        set_time_limit(0);
        $days = date("Y-m-d",strtotime("-1 day"));
        $thetime = strtotime(date("Y-m-d"), time());
        $thetime -= 24 * 60 * 60;
        $moneys = FoodOrder::find()->select([
            'site_id',
            'sum(order_price) as sumMoney',
            'count(order_price) as order_count'
        ])
            ->andWhere([
            'order_status' => FoodOrder::ORDER_STATUS_FINISH
        ])
            ->andWhere('create_at >' . $thetime)
            ->groupBy([
            'site_id'
        ])
        ->asArray()
        ->all();
        
        foreach ($moneys as $sales) {
            $sales_day = SalesDays::findOne([
                'site_id' => $sales['site_id'],
                'days' => $days
            ]);
            if (empty($sales_day)) {
                $sales_day = new SalesDays();
            }
            $sales_day->site_id = $sales['site_id'];
            $sales_day->sales = $sales['sumMoney'];
            $sales_day->order_count = $sales['order_count'];
            $sales_day->days = $days;
            $sales_day->update_at = time();
            $sales_day->save();
        }
        return true;
    }
    // 销售额补写
    public static function Setsales($site_id,$days)
    {
        set_time_limit(0);
        $i = 0;
        while ($days > 0){
            
            $day = date("Y-m-d",strtotime("-".$days." day"));
            $start_time = strtotime(date("Y-m-d",strtotime("-".$days." day")));
            $days --;
            $end_time = strtotime(date("Y-m-d",strtotime("-".$days." day")));
            
            $sales = FoodOrder::find()->select([
                'sum(order_price) as sumMoney',
                'count(order_price) as order_count'
            ])
            ->andWhere(['order_status' => FoodOrder::ORDER_STATUS_FINISH])
            ->andWhere(['between','create_at',$start_time, $end_time])
            ->andWhere(['site_id'=>$site_id])
            ->one();
            
            
            $sales_day = SalesDays::findOne([
                'site_id' => $site_id,
                'days' => $days
            ]);
            
            if (empty($sales_day)) {
                $sales_day = new SalesDays();
            }
            $sales_day->site_id = $site_id;
            $sales_day->sales = $sales['sumMoney'];
            $sales_day->order_count = $sales['order_count'];
            $sales_day->days = $days;
            $sales_day->update_at = time();
           
            $i += $sales_day->save();
        }
        return $i;
    }
}