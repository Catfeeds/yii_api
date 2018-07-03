<?php

/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;

use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\FoodOrderRefund;
use backend\modules\restaurant\models\OrderFood;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\OrderAddress;
use backend\modules\mall\models\Region;

class FoodOrderService
{

    public static function countdo($site_id)
    {
        return FoodOrder::find()->where([
            'site_id' => $site_id
        ])
            ->where([
            'in',
            'order_status',
            [
                FoodOrder::ORDER_STATUS_AGREE_REFUNDS,
                FoodOrder::ORDER_STATUS_DISAGREE_REFUNDS,
                FoodOrder::ORDER_STATUS_REFUNDS_END
            ]
        ])
            ->count();
    }

    public static function showdo($site_id, $offset, $limit)
    {
        $models = FoodOrder::find()->where([
            'site_id' => $site_id
        ])
            ->andWhere([
            'in',
            'order_status',
            [
                FoodOrder::ORDER_STATUS_AGREE_REFUNDS,
                FoodOrder::ORDER_STATUS_DISAGREE_REFUNDS,
                FoodOrder::ORDER_STATUS_REFUNDS_END
            ]
        ])
            ->orderby('create_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();
        foreach ($models as $model) {
            $model->refund = FoodOrderRefund::find()->select('note')
                ->where([
                'order_id' => $model->order_id
            ])
                ->orderBy('create_at')
                ->one()->note;
        }
        return $models;
    }

    public static function countorder($site_id, $status)
    {
        return FoodOrder::find()->where([
            'site_id' => $site_id
        ])
            ->andFilterWhere([
            'order_status' => $status
        ])
            ->count();
    }

    public static function showorder($site_id, $status, $offset, $limit)
    {
        $models = FoodOrder::find()->where([
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
            $models[$i]['order_food'] = OrderFood::find()->select([
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
                
            $models[$i]['refund'] = FoodOrderRefund::find()->select('note')
                ->where([
                'order_id' => $model['order_id']
            ])
                ->orderBy('create_at')
                ->one()->note;
           $orderaddress = OrderAddress::findOne(['order_sn'=>$model['order_sn']]);
           $address = Region::getRegions($orderaddress->twon);
           $address .= $orderaddress->address.' ';
           $address .= $orderaddress->consignee;
           $models[$i]['username'] = $orderaddress->consignee;
           $models[$i]['mobile'] = $orderaddress->mobile;
           $models[$i]['address'] = $address;
        }
        return $models;
    }

    public static function showRefund($order_id, $site_id)
    {
        $order = FoodOrder::find()->where([
            'order_id' => $order_id,
            'site_id' => $site_id
        ])
            ->asArray()
            ->one();
        if (empty($order)) {
            return null;
        }
        $order['refund'] = FoodOrderRefund::find()->where([
            'order_id' => $order_id
        ])
            ->orderby('id DESC')
            ->all();
        $orderaddress = OrderAddress::findOne(['order_sn'=>$order['order_sn']]);
        $address = Region::getRegions($orderaddress->twon);
        $address .= $orderaddress->address.' ';
        $address .= $orderaddress->consignee;
        $order['address']= $address;
        $order['username'] = $orderaddress->consignee;
        $order['mobile'] = $orderaddress->mobile;
        if (empty($order['refund'])) {
            return null;
        }
        return $order;
    }
}