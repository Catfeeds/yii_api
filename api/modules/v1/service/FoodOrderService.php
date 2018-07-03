<?php

/**
 * @date        : 2018年1月29日
 */
namespace api\modules\v1\service;

use Yii;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\FoodSKU;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderFood;
use backend\modules\restaurant\models\FoodProperty;
use backend\modules\restaurant\models\FoodPropertychild;
use backend\modules\restaurant\models\PrintTemplate;
use backend\modules\admin\models\Site;
use backend\modules\admin\models\Admin;
use common\extensions\ylyprint;
use backend\modules\restaurant\models\Prints;
use backend\modules\restaurant\models\OrderFoodAction;
use common\extensions\printcenter;
use backend\modules\restaurant\models\FoodOrderRefund;

class FoodOrderService
{

    // 下单
    public static function addorder($user_id, $site_id, $table_id, $car, $people = 0)
    {
        if (empty($user_id) || empty($site_id)) {
            return null;
        }
        $site_name = Site::findOne([
            'site_id' => $site_id
        ]);
        $admin = Admin::findOne([
            'id' => $site_name['user_id']
        ])->mobile;
        
        $order = new FoodOrder();
        $order_price = 0;
        $order->user_id = $user_id;
        $order->site_id = $site_id;
        $order->table_id = $table_id;
        $order->order_sn = time() . mt_rand(1000, 9999);
        $order->order_status = FoodOrder::ORDER_STATUS_CREATE;
        $order->order_price = $order_price;
        $order->create_at = time();
        $order->people = $people;
        
        if (! ($order->save())) {
            return null;
        }
        
        $msg = '';
        $foodmsg = '';
        foreach ($car as $food) {
            $model = Food::findOne([
                'food_id' => $food['food_id']
            ]);
            $number = $food['number'];
            
            $thefood = new OrderFood();
            $thefood->order_id = $order->order_id;
            $thefood->num = $number;
            // sku
            if (empty($food['sku'])) {
                if ($model->infinite_count != Food::NOT_INFINITE_COUNT) {
                    if ($model->store_count < $number) {
                        $msg .= $model->name . '库存不足！';
                        continue; // 菜品没有sku属性，并且不是无限库存 的情况下点菜超出库存跳出
                    } else {
                        $model->store_count -= $number;
                        $model->save();
                    }
                }
                $thefood->price = $model->price * $number;
            } else {
                $sku = FoodSKU::findOne([
                    'id' => $food['sku']
                ]);
                
                $skuname = $sku->name;
                $thefood->sku_id = $sku->id;
                $thefood->sku_name = $skuname;
                if ($sku->infinite_count == FoodSKU::NOT_INFINITE) {
                    if ($sku->store_count < $number) {
                        $msg .= $model->name . '--' . $skuname . '库存不足！';
                        continue; // 菜品不是无限库存 并且库存不足 跳出循环
                    } else {
                        $sku->store_count -= $number;
                        $sku->save();
                    }
                }
                $thefood->price = $sku->price * $number;
            }
            // 属性
            if (! empty($food['pro'])) {
                $pros = explode(";", $food['pro']);
                $thefood->pro_name = '';
                foreach ($pros as $pro) {
                    $v = explode("_", $pro);
                    $thefood->pro_name .= FoodProperty::findOne([
                        'property_id' => $v[0]
                    ])->name;
                    $thefood->pro_name .= ":" . FoodPropertychild::findOne([
                        'id' => $v[1]
                    ])->name . " ";
                }
            }
            
            $thefood->food_id = $model->food_id;
            $thefood->name = $model->name;
            $thefood->site_id = $site_id;
            $thefood->order_id = $order->order_id;
            $thefood->save();
            $foodmsg .= $thefood->name . '\r' . $number . ' ' . $model->price . ' ' . $model->price . '\r';
            $order_price += $thefood->price;
        }
        $order->order_price = $order_price;
        if (empty($msg)) {
            $msg .= '订餐完成';
        }
        // 未购买商品 返回null
        if (OrderFood::find()->where([
            'order_id' => $order->order_id
        ])->count() == 0) {
            $order->delete();
            return null;
        }
        if ($order->save()) {
            
            $theconfigs = Prints::findAll([
                'site_id' => $site_id,
                'status' => Prints::WORK_PRINT
            ]);
            foreach ($theconfigs as $theconfig) {
                if ($theconfig->brand == 1) {
                    $tem = PrintTemplate::findOne([
                        'id' => 2
                    ])->template;
                    $tem = str_replace("TITLE", $site_name['name'], $tem);
                    $tem = str_replace("ID", $order->order_id, $tem);
                    $tem = str_replace("FOOD", $foodmsg, $tem);
                    $tem = str_replace("ORDERPRICE", $order_price, $tem);
                    $tem = str_replace("PHONE", $admin, $tem);
                    
                    $ypring = new ylyprint();
                    $config = Yii::$app->params['yiliany'];
                    $ypring->action_print($config['user_id'], $theconfig->eq_number, $tem, $config['api'], $theconfig->eq_key);
                } elseif ($theconfig->brand == 2) {
                    $tem = PrintTemplate::findOne([
                        'id' => 4
                    ])->template;
                    $foodmsg = str_replace("\r", '<BR>', $foodmsg);
                    $tem = str_replace("TITLE", $site_name['name'], $tem);
                    $tem = str_replace("ID", $order->order_id, $tem);
                    $tem = str_replace("FOOD", $foodmsg, $tem);
                    $tem = str_replace("ORDERPRICE", $order_price, $tem);
                    $tem = str_replace("PHONE", $admin, $tem);
                    
                    $printcenter = new printcenter();
                    $printcenter->sendPrint($theconfig->eq_number, $theconfig->eq_key, $tem);
                }
            }
            
            $action = new OrderFoodAction();
            $action->site_id = $site_id;
            $action->order_id = $order['order_id'];
            $action->order_status = FoodOrder::ORDER_STATUS_CREATE;
            $action->order_desc = OrderFoodAction::ACTION_NOTE_CREAT;
            $action->log_time = time();
            $action->save();
            
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
    public static function addtheorder($order_id, $car)
    {
        if (empty($user_id) || empty($site_id)) {
            return null;
        }
        
        $order = FoodOrder::findOne([
            'order_id' => $order_id
        ]);
        
        foreach ($car as $food) {
            $model = Food::findOne([
                'food_id' => $food['food_id']
            ]);
            $number = $food['number'];
            
            $thefood = new OrderFood();
            $thefood->order_id = $order->order_id;
            $thefood->num = $number;
            // sku
            if (empty($food['sku_id'])) {
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
                $sku = FoodSKU::findOne([
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
                $pros = explode(";", $food['pro']);
                $thefood->pro_name = '';
                foreach ($pros as $pro) {
                    $v = explode("_", $pro);
                    $thefood->pro_name .= FoodProperty::findOne([
                        'property_id' => $v[0]
                    ])->name;
                    $thefood->pro_name .= ":" . FoodPropertychild::findOne([
                        'id' => $v[1]
                    ])->name . " ";
                }
            }
            
            $thefood->food_id = $model->food_id;
            
            $thefood->save();
            
            $order_price += $thefood->price;
        }
        $order->order_price += $order_price;
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

    // 查询未完成订单//堂食订单
    public static function shownotpay($user_id, $table_id = '', $site_id = '')
    {
        return FoodOrder::find()->where([
            'user_id' => $user_id
        ])
            ->andWhere([
            'is_out' => 0
        ])
            ->andFilterWhere([
            'table_id' => $table_id,
            'site_id' => $table_id
        ])
            ->orderBy('create_at DESC')
            ->all();
    }

    // 查看订单详情
    public static function showview($user_id, $order_id)
    {
        if (empty($user_id)) {
            return null;
        }
        $order = FoodOrder::findOne([
            '$order_id' => $order_id,
            'user_id' => $user_id
        ]);
        $orderfoods = OrderFood::findAll([
            '$order_id' => $order_id
        ]);
        return [
            'order' => $order,
            'foods' => $orderfoods
        ];
    }

    // 查看我的订单列表//堂食订单
    public static function showmyorderwithpage($user_id, $offset, $limit, $site_id = null)
    {
        return FoodOrder::find()->where([
            'user_id' => $user_id
        ])
            ->andWhere([
            'is_out' => 0
        ])
            ->andFilterWhere([
            'site_id' => $site_id
        ])
            ->orderBy('create_at DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    public static function showmyordercount($user_id, $site_id = null)
    {
        return FoodOrder::find()->where([
            'user_id' => $user_id
        ])
            ->andWhere([
            'is_out' => 0
        ])
            ->andFilterWhere([
            'site_id' => $site_id
        ])
            ->count();
    }

    public static function showbyapply($site_id, $offset, $limit)
    {
        return FoodOrder::find()->where([
            'site_id' => $site_id,
            'order_status' => FoodOrder::ORDER_STATUS_APPLY_REDUNDS
        ])
            ->orderby('create_at')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    public static function showbyapplycount($site_id)
    {
        return FoodOrder::find()->where([
            'site_id' => $site_id,
            'order_status' => FoodOrder::ORDER_STATUS_APPLY_REDUNDS
        ])->count();
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
    public static function showorder($site_id, $status,$offset, $limit)
    {
        return FoodOrder::find()->select([FoodOrder::tableName().'.*',FoodOrderRefund::tableName().'.note'])
        ->where([
            FoodOrder::tableName().'.site_id' => $site_id
        ])->andFilterWhere([
            FoodOrder::tableName().'.order_status' => $status
        ])
        ->leftJoin(FoodOrderRefund::tableName(),FoodOrder::tableName().'.order_id ='.FoodOrderRefund::tableName().'.order_id')
        ->orderby(FoodOrderRefund::tableName().'.create_at')
        ->offset($offset)
        ->limit($limit)
        ->all();
    }
    
    public static function showRefund($order_id,$site_id)
    {
        $order = FoodOrder::find()->where(['order_id'=>$order_id,'site_id' => $site_id])->one();
        if(empty($order)){
            return null;
        }
        $order->refund = FoodOrderRefund::find()->where(['order_id'=>$order_id])->one();
        if(empty($order->refund)){
            return null;
        }
        return $order;
    }
}