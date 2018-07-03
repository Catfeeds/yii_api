<?php
/**
 * @date        : 2018年1月29日
 */
namespace api\modules\v1\service;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\FoodSKU;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderFood;

class OrderService
{
    // 下单
    public static function addorder ($user_id, $site_id, $table_id, $car)
    {
        if (empty($user_id) || empty($site_id)) {
            return null;
        }
        
        $order = new FoodOrder();
        
        $msg = '';
        $order_price = 0;
        $order->user_id = $user_id;
        $order->site_id = $site_id;
        $order->table_id = $table_id;
        $order->order_sn = 'sn' . time() . mt_rand(1000, 9999);
        $order->order_status = FoodOrder::ORDER_STATUS_CREATE;
        $order->order_price = $order_price;
        
        if (! ($order->save())) {
            return null;
        }
        foreach ($car as $food) {
            $model = Food::findOne(
                    [
                            'food_id' => $food['food_id']
                    ]);
            $number = $food['number'];
            
            $thefood = new OrderFood();
            $thefood->order_id = $order->order_id;
            $thefood->num = $number;
            // sku
            if (empty($food['sku_id'])) {
                if($model->sku == Food::HAVE_SKU){
                    $msg .= $model->name . '未选中属性！';//菜品有sku属性 但是未选择
                    continue;
                }
                if ($model->infinite_count != Food::NOT_INFINITE_COUNT) {
                    if ($model->store_count < $number) {
                        $msg .= $model->name . '库存不足！';
                        continue; // 菜品没有sku属性，并且不是无限库存 的情况下点菜超出库存跳出
                    } else {
                        $model->store_count -= $number;
                        $model->save();
                    }
                }
                $thefood->price = $model->price;
            } else {
                $sku = FoodSKU::findOne(
                        [
                                'id' => $food['sku_id']
                        ]);
                $skuname = $sku->name;
                if ($sku->infinite_count == FoodSKU::NOT_INFINITE) {
                    if ($sku->store_count < $number) {
                        $msg .= $model->name . '--' . $skuname . '库存不足！';
                        continue; // 菜品不是无限库存 并且库存不足 跳出循环
                    } else {
                        $sku->store_count -= $number;
                        $sku->save();
                    }
                }
                $thefood->price = $sku->price;
            }
            // 属性
            if (! empty($food['pro'])) {
                $thefood->pro_name = $food['pro'];
            }
            
            $thefood->food_id = $model->food_id;
            
            $thefood->save();
            
            $order_price += $thefood->price;
        }
        $order->order_price = $order_price;
        if (empty($msg)) {
            $msg = '订餐完成';
        }
        if ($order->save()) {
            return [
                    'Order' => $order,
                    'msg' => $msg
            ];
        } else {
            $order->delete();
            return null;
        }
    }

    // 续单
    
    // 查询未完成订单
    public static function shownotpay ($user_id, $table_id = '', $site_id = '')
    {
        
        return FoodOrder::find()->where('user_id = :user_id', 
                [
                        ':user_id' => $user_id
                ])
            ->andFilterWhere(['table_id' => $table_id,'site_id' => $table_id])
            ->orderBy('create_at DESC')
            ->all();
    }

    // 查看订单详情
    public static function showview ($user_id, $order_id)
    {
        if (empty($user_id)) {
            return null;
        }
        $order = FoodOrder::findOne(
                [
                        '$order_id' => $order_id,
                        'user_id' => $user_id
                ]);
        $orderfoods = OrderFood::findAll(
                [
                        '$order_id' => $order_id
                ]);
        return [
                'order' => $order,
                'foods' => $orderfoods
        ];
    }

    // 查看我的订单列表
    public static function showmyorderwithpage ($user_id, $offset, $limit, 
            $site_id = '')
    {
        return FoodOrder::find()->where('user_id = :user_id', 
                [
                        ':user_id' => $user_id
                ])
            ->andFilterWhere([
                'site_id' => $site_id
        ])
            ->orderBy('create_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    public static function showmyordercount ($user_id, $site_id = '')
    {
        return FoodOrder::find()->where('user_id = :user_id', 
                [
                        ':user_id' => $user_id
                ])
            ->andFilterWhere([
                'site_id' => $site_id
        ])
            ->count();
    }
}


