<?php
namespace h5\modules\v1\controllers;
date_default_timezone_set('Asia/Shanghai');
use Yii;
use h5\base\BaseController;
use api\modules\v1\models\FoodCar;
use backend\modules\mall\models\Address;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderAddress;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\OrderFood;
use backend\modules\restaurant\models\FoodSKU;
use backend\modules\restaurant\models\FoodProperty;
use backend\modules\restaurant\models\FoodPropertychild;
use backend\modules\restaurant\models\OrderFoodAction;
use yii\data\Pagination;
use api\modules\v1\models\FoodCarNum;
use h5\modules\v1\models\UserSite;
use h5\modules\v1\service\JsApiPay;
use backend\modules\ucenter\models\WeixinUser;
use h5\modules\v1\service\printService;
use h5\modules\v1\service\WeChatJsApiPay;
use backend\modules\restaurant\models\FoodOrderRefund;
use backend\modules\admin\models\SiteConfig;
use backend\modules\admin\models\SiteWxconfig;

/**
 * Class OrderController
 *
 * @package api\modules\v1\controllers
 */
class OrderController extends BaseController
{

    public function actionRefund()
    {
        $user = $this->getUser();
        $site_id = $this->getSite();
        $order_sn = Yii::$app->request->post('order_sn'); // 退货编号
        $note = Yii::$app->request->post('note'); // 退货理由
        $order = FoodOrder::findOne([
            'order_sn' => $order_sn,
            'user_id' => $user->id
        ]);
        if (empty($order)) {
            return $this->jsonFail($order_sn, '未查询到此订单');
        }
        if ($order->create_at - time() > 7 * 24 * 60 * 60) {
            return $this->jsonFail($order_sn, '暂不支持一周以上订单退货！');
        }
        $refund = FoodOrderRefund::findOne([
            'order_id' => $order->order_id
        ]);
        if (! empty($refund)) {
            return $this->jsonFail($refund, '订单已经申请退货，请耐心等待');
        }
        // 未付款直接取消订单。
        if ($order->order_status == FoodOrder::ORDER_STATUS_CREATE) {
            $action = new OrderFoodAction();
            $action->site_id = $order->site_id;
            $action->order_id = $order->order_id;
            $action->order_status = FoodOrder::ORDER_STATUS_CANCEL;
            $action->order_desc = OrderFoodAction::ACTION_NOTE_CANCEL;
            $action->log_time = time();
            $action->action_note = $note;
            $action->save();
            $order->order_status = FoodOrder::ORDER_STATUS_CANCEL;
            if ($order->save()) {
                return $this->jsonSuccess($order_sn, '取消订单成功');
            }
        } elseif ($order->order_status == FoodOrder::ORDER_STATUS_PAY || $order->order_status == FoodOrder::ORDER_STATUS_GETORDER || $order->order_status == FoodOrder::ORDER_STATUS_SHIPPING || $order->status == FoodOrder::ORDER_STATUS_FINISH) {
            $action = new OrderFoodAction();
            $action->site_id = $order->site_id;
            $action->order_id = $order->order_id;
            $action->order_status = FoodOrder::ORDER_STATUS_REDUNDS;
            $action->order_desc = OrderFoodAction::STATUS_DESC_APPLY_REDUNDS;
            $action->log_time = time();
            $action->action_note = $note;
            $action->save();
            
            $refund = new FoodOrderRefund();
            $refund->order_id = $order->order_id;
            $refund->note = $note;
            $refund->create_at = time();
            $refund->order_status = $order->order_status;
            $refund->user_id = $user->id;
            $refund->save();
            
            $order->order_status = FoodOrder::ORDER_STATUS_REDUNDS;
            $order->update_at = time();
            if ($refund->save() && $order->save()) {
                return $this->jsonSuccess($order_sn, '已提交退货申请');
            }
        } else {
            return $this->jsonFail($order_sn, '此订单无法申请退货');
        }
    }

    public function actionCreate()
    {
        $user = $this->getUser();
        if (empty($user->mobile)) {
            return $this->jsonFail('', '未绑定手机号');
        }
        $user_id = $user->id;
        $site_id = $this->getSite();
        $address_id = Yii::$app->request->post('address_id');
        $deliverytime = Yii::$app->request->post('deliverytime');
        $note = Yii::$app->request->post('note');
        $people = Yii::$app->request->post('people');
        if (empty($people)) {
            $people = 1;
        }
        if (empty($deliverytime)) {
            $deliverytime = time();
        }
        
        $cars = FoodCar::find()->where([
            'user_id' => $user_id,
            FoodCar::tableName() . '.site_id' => $site_id,
            'status' => FoodCar::STATUS_H5
        ])
            ->joinWith('food')
            ->joinWith('sku')
            ->asArray()
            ->all();
        $address = Address::findTheAddressArray($user_id, $address_id);
        if (empty($cars)) {
            return $this->jsonFail([], '购物车为空');
        }
        if (empty($address)) {
            return $this->jsonFail([], '收货地址不存在');
        }
        $order_price = 0;
        $box_price = 0;
        $shipping_price = SiteConfig::find()->select('shipping_price')
            ->where([
            'site_id' => $site_id
        ])
            ->one()->shipping_price;
        if (empty($shipping_price)) {
            $shipping_price = Yii::$app->params['site']['shipping_price'];
        }
        $order = new FoodOrder();
        $order->user_id = $user_id;
        $order->site_id = $site_id;
        $order->table_id = 0;
        $order->order_sn = $site_id.$user_id.time() . mt_rand(1000, 9999);
        $order->order_status = FoodOrder::ORDER_STATUS_CREATE;
        $order->order_price = $order_price;
        $order->box_price = $box_price;
        $order->shipping_price = $shipping_price;
        $order->create_at = time();
        $order->update_at = time();
        $order->people = $people;
        $order->is_out = 1;
        $order->deliverytime = $deliverytime;
        $order->note = $note;
        $order->save();
        $order_address = new OrderAddress();
        $order_address->load($address, '');
        $order_address->order_sn = $order->order_sn;
        $order_address->save();
        $msg = '';
        $foodmsg = '';
        foreach ($cars as $food) {
            $model = Food::findOne([
                'food_id' => $food['food_id']
            ]);
            if(empty($model))
            {
                $msg .= '菜品不存在';
            }
            if($model->is_del == Food::IS_DEL || $model->is_on_sale == Food::NOT_ON_SALE)
            {
                $msg .= $model->name . '菜品下架';
                continue;
            }
            $number = $food['number'];
            
            $thefood = new OrderFood();
            $thefood->order_id = $order->order_id;
            $thefood->num = $number;
            // sku
            if (empty($food['sku'])) {
                if ($model->infinite_count == Food::NOT_INFINITE_COUNT) {
                    if ($model->store_count < $number) {
                        $msg .= $model->name . '库存不足！';
                        continue; // 菜品没有sku属性，并且不是无限库存 的情况下点菜超出库存跳出
                    } else {
                        $model->store_count -= $number;
                        $model->save();
                    }
                }
                $thefood->price = $model->price * $number;
                $thefood->box_price = $model->box_price * $number;
            } else {
                $sku = FoodSKU::findOne([
                    'id' => $food['sku'],
                    'food_id' => $food['food_id']
                ]);
                if (! empty($sku)) {
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
                    $thefood->box_price = $sku->box_price * $number;
                }
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
            $box_price += $thefood->box_price;
        }
        $order->order_price = $order_price;
        $order->box_price = $box_price;
        if (empty($msg)) {
            $msg .= '订餐完成';
        }
        // 未购买商品 返回null
        if (OrderFood::find()->where([
            'order_id' => $order->order_id
        ])->count() == 0) {
            $order->delete();
            $order_address->delete();
            return $this->jsonFail($msg, '下单失败');
        }
        
        if ($order->save()) {
            
            $action = new OrderFoodAction();
            $action->site_id = $site_id;
            $action->order_id = $order['order_id'];
            $action->order_status = FoodOrder::ORDER_STATUS_CREATE;
            $action->action_note = $note . OrderFoodAction::ACTION_NOTE_PAY;
            $action->log_time = time();
            $action->order_desc = OrderFoodAction::ACTION_NOTE_CREAT;
            $action->save();
            // 购物车价格清空
            FoodCarNum::deleteAll([
                'user_id' => $user_id,
                'site_id' => $site_id,
                'status' => FoodCarNum::STATUS_H5
            ]);
            UserSite::deleteAll([
                'user_id' => $user_id,
                'site_id' => $site_id
            ]);
            FoodCar::deleteAll([
                'user_id' => $user_id,
                'site_id' => $site_id,
                'status' => FoodCarNum::STATUS_H5
            ]);
            return $this->jsonSuccess([
                'order' => $order,
                'address' => $address
            ], '下单成功');
        } else {
            $order->delete();
            $order_address->delete();
            return $this->jsonFail([], '下单失败');
        }
    }

    public function actionShowmyorder()
    {
        $user_id = $this->getUserId();
        $status = Yii::$app->request->post('status');
        $count = FoodOrder::find()->where('user_id = :user_id', [
            ':user_id' => $user_id
        ])
            ->andWhere([
            'is_out' => FoodOrder::ORDER_IS_OUT
        ])->andFilterWhere(['order_status'=>$status])
            ->count();
        
        $page_info = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
        ]);
        
        $page_info->setPage($this->getParam('page') - 1);
        $model = FoodOrder::find()->where('user_id = :user_id', [
            ':user_id' => $user_id
        ])
            ->andWhere([
            'is_out' => FoodOrder::ORDER_IS_OUT
            ])->andFilterWhere(['order_status'=>$status])
            ->orderBy('create_at DESC')
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        if (! empty($model)) {
            return $this->jsonSuccessWithPage($model, $page_info, '查询成功');
        } else {
            return $this->jsonFail([], '查询失败');
        }
    }

    public function actionShoworder()
    {
        $user_id = $this->getUserId();
        $order_id = Yii::$app->request->get('order_id');
        $order = FoodOrder::find()->where([
            'user_id' => $user_id,
            'order_id' => $order_id
        ])
            ->andWhere([
            'is_out' => FoodOrder::ORDER_IS_OUT
        ])
            ->one();
        $orderfood = OrderFood::find()->where([
            'order_id' => $order_id
        ])->all();
        if (empty($order)) {
            return $this->jsonFail('', '未查询到订单');
        }
        return $this->jsonSuccess([
            'order' => $order,
            'order_food' => $orderfood
        ], '查询成功');
    }

    public function actionPayorder()
    {
        $user = $this->getUser();
        $site_id = $this->getSite();
        
        if (empty($user->mobile)) {
            return $this->jsonFail('', '未绑定手机号');
        }
        $openId = $this->getOpenId($user->id, $site_id);
//         $openId = WeixinUser::find()->select('openid')
//             ->where([
//             'uid' => $user->id,
//             'site_id'=>$site_id
//         ])->one()->openid;
        
        $order_sn = Yii::$app->request->get('order_sn');
        if (empty($order_sn)) {
            return $this->jsonFail([], '参数不完整');
        }
        $order = FoodOrder::findOne([
            'order_sn' => $order_sn
        ]);
        if (empty($order)) {
            return $this->jsonFail([], '未查询到此订单');
        }
        if ($order->order_status != FoodOrder::ORDER_STATUS_CREATE) {
            return $this->jsonFail([], '订单已付款');
        }
        
        $price = $order->order_price * 100;
        $box_price = $order->box_price * 100;
        $ship_price = $order->shipping_price * 100;
        $price += $box_price;
        $price += $ship_price;
        
        $jsPay = new WeChatJsApiPay();
        
        $array = [
            'openid' => $openId,
            'body' => "小牛科技",
            'price' => $price,
            'order_sn' => $order_sn,
            'notifyUrl' => Yii::$app->params['wx_h5_pay']['notifyUrl']
        ];
        $res = WeChatJsApiPay::pay($array, $site_id);
        return $this->jsonSuccess($res);
        
        // ③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
    /**
     * 注意：
     * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
     * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
     * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
     */
    }

    public function actionPaynotify()
    {
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        // 错误抛出
        if ($postObj === false) {
            die('parse xml error');
        }
        if ($postObj->return_code != 'SUCCESS') {
            die($postObj->return_msg);
        }
        if ($postObj->result_code != 'SUCCESS') {
            die($postObj->err_code);
        }
        $sn = $postObj->out_trade_no;
        $other_sn = $postObj->transaction_id;
        $model = FoodOrder::find()->where([
            'order_sn' => $sn,
            'order_status'=>FoodOrder::ORDER_STATUS_CREATE
        ])->one();
        
        if(!empty($model)){
            $model->order_status = FoodOrder::ORDER_STATUS_PAY;
            $model->other_sn = $other_sn;
            $model->update_at = time();
            $action = new OrderFoodAction();
            $action->site_id = $model->site_id;
            $action->order_id = $model->order_id;
            $action->order_status = FoodOrder::ORDER_STATUS_PAY;
            $action->order_desc = OrderFoodAction::ACTION_NOTE_PAY;
            $action->log_time = time();
            $action->action_note = OrderFoodAction::ACTION_NOTE_PAY;
            $action->save();
            $model->save();
            // 打印单子
            $model->order_status = FoodOrder::ORDER_STATUS_GETORDER;
            printService::OutAllWorkPrint($sn);
            echo($model->save(false) ? 'SUCCESS' : 'ERROR');
        }
        
        echo('ERROR');
    }
}
