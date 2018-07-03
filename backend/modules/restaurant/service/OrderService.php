<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;

use Yii;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderFoodAction;
use backend\modules\restaurant\models\OrderFood;
use backend\modules\restaurant\models\Tables;
use backend\modules\mall\models\Order;
use backend\modules\ucenter\models\User;
use backend\modules\restaurant\models\Food;

class OrderService
{

    public static function showwait($site_id, $offset, $limit)
    {
        return FoodOrder::find()->where('site_id = :site_id', [
            ':site_id' => $site_id
        ])
            ->andWhere([
            'is_out' => FoodOrder::ORDER_IS_OUT
        ])
            ->andWhere([
            'in',
            'order_status',
            [
                FoodOrder::ORDER_STATUS_PAY,
                FoodOrder::ORDER_STATUS_GETORDER
            ]
        ])
            ->orderby('create_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    public static function showwaitcount($site_id)
    {
        return FoodOrder::find()->where('site_id = :site_id', [
            ':site_id' => $site_id
        ])
            ->andWhere([
            'is_out' => FoodOrder::ORDER_IS_OUT
        ])
            ->andWhere([
            'in',
            'order_status',
            [
                FoodOrder::ORDER_STATUS_PAY,
                FoodOrder::ORDER_STATUS_GETORDER
            ]
        ])
            ->count();
    }

    public static function listbystatus($site_id, $status = '', $offset, $limit)
    {
        $models = FoodOrder::find()->select([
            'order_id',
            'order_sn',
            'user_id',
            'is_out',
            'box_price',
            'order_price',
            'shipping_price',
            'create_at'
        ])
            ->where([
            'site_id' => $site_id
        ])
            ->andFilterWhere([
            'order_status' => $status
        ])
            ->orderby('create_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->all();
        foreach ($models as $i => $model) {
            $user = User::find()->select('mobile')
                ->where([
                'id' => $model['user_id']
            ])
                ->one();
            $foods = OrderFood::find()->select([
                OrderFood::tableName() . '.name',
                OrderFood::tableName() . '.num',
                OrderFood::tableName() . '.price',
                OrderFood::tableName() . '.box_price',
                Food::tableName() . '.image'
            ])
            ->from(OrderFood::tableName())
                ->where([
                'order_id' => $model['order_id']
            ])
                ->leftJoin(Food::tableName(), OrderFood::tableName() . '.food_id = ' . Food::tableName() . '.food_id')
                ->asArray()
                ->all();
            $models[$i]['user'] = $user;
            $models[$i]['foods'] = $foods;
        }
        return $models;
    }

    public static function countbystatus($site_id = '', $status)
    {
        return FoodOrder::find()->where('site_id = :site_id', [
            ':site_id' => $site_id
        ])
            ->andFilterWhere([
            'order_status' => $status
        ])
            ->count();
    }

    public static function showbysite($site_id, $offset, $limit)
    {
        return FoodOrder::find()->where('site_id = :site_id', [
            ':site_id' => $site_id
        ])
            ->orderby('create_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    public static function showbysitecount($site_id)
    {
        return FoodOrder::find()->where('site_id = :site_id', [
            ':site_id' => $site_id
        ])->count();
    }

    public static function showfoods($order_id)
    {
        return OrderFood::findAll([
            'order_id' => $order_id
        ]);
    }
    
    public static function allfoods($order_id)
    {
        return OrderFood::find()->select([
            OrderFood::tableName() . '.name',
            OrderFood::tableName() . '.num',
            OrderFood::tableName() . '.price',
            OrderFood::tableName() . '.box_price',
            Food::tableName() . '.image'
        ])
        ->from(OrderFood::tableName())
        ->where([
            'order_id' => $order_id
        ])
        ->leftJoin(Food::tableName(), OrderFood::tableName() . '.food_id = ' . Food::tableName() . '.food_id')
        ->asArray()
        ->all();
    }
    
    public static function finishorder($order_id, $action_note = '')
    {
        $order = FoodOrder::findbyid($order_id);
        if (empty($order)) {
            return false;
        }
        if ($order->order_status == FoodOrder::ORDER_STATUS_FINISH) {
            return false;
        }
        $order->order_status = FoodOrder::ORDER_STATUS_FINISH;
        $order->update_at = time();
        
        $site_id = $order->site_id;
        $table_id = $order->table_id;
        
        $table = Tables::findOne([
            'site_id' => $site_id,
            '$table_id' => $table_id
        ]);
        
        $table->status = Tables::TABLE_NULL_PEOPLE;
        $table->save();
        
        $action = new OrderFoodAction();
        $action->order_id = $order_id;
        $action->order_status = FoodOrder::ORDER_STATUS_FINISH;
        $action->order_desc = OrderFoodAction::ACTION_NOTE_FINISH;
        $action->action_note = $action_note;
        $action->log_time = time();
        $action->save();
        
        // OrderFood::updateAll(['finish_num'=>'num'],['order_id'=>$order_id]);
        
        return $order->save();
    }

    public static function shippingorder($order_id, $action_note = '')
    {
        $order = FoodOrder::findbyid($order_id);
        if (empty($order)) {
            return false;
        }
        if ($order->order_status == FoodOrder::ORDER_STATUS_SHIPPING) {
            return false;
        }
        $order->order_status = FoodOrder::ORDER_STATUS_SHIPPING;
        $order->update_at = time();
        $site_id = $order->site_id;
        $table_id = $order->table_id;
        
        if ($table_id != 0) {
            $table = Tables::findOne([
                'site_id' => $site_id,
                'table_id' => $table_id
            ]);
            
            $table->status = Tables::TABLE_NULL_PEOPLE;
            $table->save();
        }
        
        $action = new OrderFoodAction();
        $action->order_id = $order_id;
        $action->order_status = FoodOrder::ORDER_STATUS_SHIPPING;
        $action->order_desc = OrderFoodAction::ACTION_NOTE_SHIPPING;
        $action->action_note = $action_note;
        $action->log_time = time();
        $action->save();
        
        // OrderFood::updateAll(['finish_num'=>$num],['order_id'=>$order_id]);
        
        return $order->save();
    }

    public static function finishautomatic()
    {
        return FoodOrder::updateAll([
            'order_status' => FoodOrder::ORDER_STATUS_FINISH
        ], 'order_status =' . FoodOrder::ORDER_STATUS_SHIPPING . ' and update_at <' . (time() - Yii::$app->params['autofinishtime']));
    }

    public static function EroorOrderAutomatic()
    {
        return FoodOrder::updateAll([
            'order_status' => FoodOrder::ORDER_STATUS_ERROR
        ], 'order_status =' . FoodOrder::ORDER_STATUS_CREATE . ' and update_at <' . (time() - Yii::$app->params['autofinishtime']));
    }
}


