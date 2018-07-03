<?php

/**
 * @author Jason
 * @date 2016-08-08
 * @copyright Copyright © 2016年 EleTeam
 */

//商城订单

namespace api\modules\v1\controllers;
use api\base\BaseController;
use Yii;
use backend\modules\mall\models\Order;
use backend\modules\mall\models\Goods;
use backend\modules\mall\models\Address;
use backend\modules\mall\models\OrderGoods;
use common\utils\StringUtil;
use backend\modules\mall\models\OrderAction;
use backend\modules\mall\service\Goods_service;
use yii\data\Pagination;
use common\extensions\wxpay\example\JsApiPay;
use common\extensions\wxpay\lib\WxPayUnifiedOrder;
use common\extensions\wxpay\lib\WxPayConfig;
use common\extensions\wxpay\lib\WxPayApi;
use backend\modules\mall\models\ShippingArea;
use backend\modules\mall\models\Shipping;
use backend\modules\mall\models\AreaRegion;
use backend\modules\mall\models\SpecGoodsPrice;

/**
 * 订单控制器
 * Class OrderController
 *
 * @package api\modules\v1\controllers
 */
class OrderController extends BaseController
{

    public $address;

    /**
     * 查看订单列表
     */
    public function actionIndex ()
    {
        $user_id = $this->getUserId();
        $page_info = new Pagination(
                [
                        'totalCount' => Order::find()->where('user_id=:user_id', 
                                [
                                        ':user_id' => $user_id
                                ])->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $orders = Order::find()->where('user_id=:user_id', 
                [
                        ':user_id' => $user_id
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($orders, $page_info);
    }

    /**
     * 查看待付款订单列表
     */
    public function actionListPendingPay ()
    {
        $user_id = $this->getUserId();
        $page_info = new Pagination(
                [
                        'totalCount' => Order::find()->where(
                                'user_id=:user_id and pay_status=:status', 
                                [
                                        ':user_id' => $user_id,
                                        ':status' => Order::PAY_STATUS_ACTIVE
                                ])->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $orders = Order::find()->where(
                'user_id=:user_id and pay_status=:status', 
                [
                        ':user_id' => $user_id,
                        ':status' => Order::PAY_STATUS_ACTIVE
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($orders, $page_info);
    }

    /**
     * 查看待收货订单列表
     */
    public function actionListDelivering ()
    {
        $user_id = $this->getUserId();
        $orders = Order::find()->where('user_id=:user_id and status=:status', 
                [
                        ':user_id' => $user_id,
                        ':status' => Order::STATUS_ACTIVE
                ])
            ->orderBy('id desc')
            ->limit(30)
            ->all();
        
        return $this->jsonSuccess(
                [
                        'orders' => $orders
                ]);
    }

    /**
     * 查看交易完成订单列表
     */
    public function actionListFinished ()
    {
        $user_id = $this->getUserId();
        $orders = Order::find()->where('user_id=:user_id and status=:status', 
                [
                        ':user_id' => $user_id,
                        ':status' => Order::STATUS_ACTIVE
                ])
            ->orderBy('id desc')
            ->limit(30)
            ->all();
        
        return $this->jsonSuccess(
                [
                        'orders' => $orders
                ]);
    }

    public function actionView ($id)
    {  
        $user_id = $this->getUserId();
    }

    /**
     * 创建订单
     * 最简单的创建 无购物车概念
     * 无收货地址id 收货地址为默认地址
     */
    public function actionCreateorder ()
    {
        $user_id = $this->getUserId();
        
        $StringUtile = new StringUtil();
        // 商品信息
        $goods_id = Yii::$app->request->post('goods_id');
        if (empty($goods_id)) {
            return $this->jsonFail([], '没有商品id');
        }
        $goods = Goods::find()->select(
                'store_count,weight,shop_price,last_update')
            ->where('goods_id=:goods_id', 
                [
                        'goods_id' => $goods_id
                ])
            ->one();
        $store_count = $goods['store_count'];
        $theprice = $goods['shop_price'];
        
        // 是否购买的是带有属性的商品
        $item_id = Yii::$app->request->post('specgoods_id');
        if ($goods->sku == Goods::HAVE_SKU && empty(itemid)) {
            return $this->jsonFail($goods->goods_name, '必须选择商品规格');
        }
        $spec_key = '';
        $spec_key_name = '';
        if (! empty($item_id)) {
            $specgoods = SpecGoodsPrice::getbyitemsid($item_id, $goods);
            if (! empty($specgoods)) {
                $store_count = $specgoods['store_count'];
                $theprice = $specgoods['price'];
                $spec_key = $specgoods['key'];
                $spec_key_name = $specgoods['key_name'];
            } else {
                return $this->jsonFail([], '请检查商品属性是否对应');
            }
        }
        $goods_num = Yii::$app->request->post('goods_num');
        if (empty($goods_num)) {
            $goods_num = 1;
        }
        if ($store_count < $goods_num) {
            return $this->jsonFail([], '没有足够的库存');
        }
        $goods_weight = $goods['weight'] * $goods_num;
        $goods_price = $theprice * $goods_num; // 订单总价
                                               // 订单
        $order = new Order();
        $order->user_id = $user_id;
        $order->pay_name = Yii::$app->request->post('pay_name');
        $order->goods_price = $goods_price; // 订单总价
        $order->order_sn = $StringUtile->genSerialNum();
        
        if (empty($order->pay_name)) {
            $order->pay_name = Order::STATUS_PAY_NAME; // 默认付款方式
        }
        
        // 收货地址
        $address_id = Yii::$app->request->post('address_id');
        if (empty($address_id)) {
            $address = Address::find()->where(
                    'user_id=:user_id and is_default = :is_default', 
                    [
                            ':user_id' => $user_id,
                            ':is_default' => Address::STATUS_IS_DEFAULT
                    ])
                ->asArray()
                ->one();
        } else {
            $address = Address::find()->where(
                    'address_id=:address_id and user_id = :user_id', 
                    [
                            ':address_id' => $address_id,
                            ':user_id' => $user_id
                    ])->one();
        }
        
        if (! $address) {
            return $this->jsonFail([], '没有找到地址');
        }
        $order->province = $address['province'];
        $order->city = $address['city'];
        $order->district = $address['district'];
        $order->twon = $address['twon'];
        $order->address = $address['address'];
        $order->consignee = $address['consignee'];
        $order->mobile = $address['mobile'];
        $order->add_time = time();
        
        if ($goods['is_free_shipping'] != Goods::FREE_SHIPPING) {
            // 不包邮
            $shipping_price = 0;
            $shipping_code = Yii::$app->request->post('shipping');
            $areashippings = AreaRegion::find()->where('region_id = :region_id', 
                    [
                            ':region_id' => $address['province']
                    ])->all();
            if (empty($areashippings)) {
                $shipping = ShippingArea::find()->select('config')
                    ->where('shipping_code = :shipping_code and is_default = 1', 
                        [
                                ':shipping_code' => $shipping_code
                        ])
                    ->asArray()
                    ->one();
            } else {
                $shipping = ShippingArea::find()->select('config')
                    ->where('shipping_code = :shipping_code', 
                        [
                                ':shipping_code' => $shipping_code
                        ])
                    ->andWhere(
                        'shipping_area_id in ' .
                                 $areashippings['shipping_area_id'])
                    ->asArray()
                    ->one();
            }
            // 反序列化邮费模板
            $the_config = unserialize($shipping['config']);
            if ($goods_weight <= $the_config['first_weight']) {
                $shipping_price = $the_config['money'];
            } else {
                $shipping_price += $the_config['money'];
                $goods_weight -= $the_config['first_weight'];
                $thecount = ceil($goods_weight / $the_config['second_weight']);
                $shipping_price += $the_config['add_money'] * $thecount;
            }
            $order->shipping_price = $shipping_price;
        } else {
            $order->shipping_price = 0;
        }
        if (! ($order->load($order, '') && $order->save())) {
            return $this->jsonFail([], '保存订单失败');
        } else {
            // 订单包含商品
            $order_goods = new OrderGoods();
            $order_goods->order_id = $order->order_id;
            $order_goods->goods_id = $goods_id;
            $order_goods->goods_num = $goods_num;
            $order_goods->goods_price = $theprice; // 商品价格
            $order_goods->spec_key = $spec_key;
            $order_goods->spec_key_name = $spec_key_name;
            $order_goods->save();
            
            $order_action = new OrderAction();
            $order_action->order_id = $order->order_id;
            $order_action->log_time = time();
            $order_action->status_desc = "提交订单";
            
            if (! empty($specgoods)) {
                $specgoods->store_count = $specgoods['store_count'] - $goods_num;
                if (! ($specgoods->save())) {
                    $order->deleted();
                    return $this->jsonFail([], '添加失败');
                }
            }
            // 商品的数量随sku变化。
            $thegoods = Goods_service::get_child_one_api($goods_id);
            $thegoods->store_count = $store_count - $goods_num;
            $thegoods->last_update = time();
            if (! ($thegoods->save())) {
                $order->deleted();
                return $this->jsonFail([], '添加失败');
            }
            if ($order->load($order_goods, '') && $order->save() &&
                     $order_goods->save() && $order_action->save()) {
                return $this->jsonSuccess(
                        [
                                $order,
                                $order_goods,
                                $order_action
                        ], '创建订单成功');
            } else {
                $order->deleted();
                return $this->jsonFail([], '添加失败');
            }
        }
    }

    /**
     * 创建订单
     * 有购物车概念的订单
     * 无收货地址id 收货地址为默认地址
     */
    public function actionCreateorderbycart ()
    {
        $user_id = $this->getUserId();
        $StringUtile = new StringUtil();
        // 购物车以post方式提交二维数组
        $cars = Yii::$app->request->post('car');
        $goods = null;
        $theprice = 0;
        $theweight = 0;
        foreach ($cars as $good) {
            $goods_id = $good['goods_id'];
            $goods_num = $good['goods_num'];
            if (! empty($good['specgoods_id'])) {
                $item_id = $good['specgoods_id'];
            }
            $goods = Goods::find()->select(
                    'goods_name,weight,store_count,shop_price,last_update,is_free_shipping')
                ->where('goods_id=:goods_id', 
                    [
                            'goods_id' => $goods_id
                    ])
                ->one();
            if ($goods->sku == Goods::HAVE_SKU && $good['specgoods_id']) {
                return $this->jsonFail($goods, '此商品必须有sku值');
            }
            $store_count = $goods['store_count'];
            $goodsprice = $goods['shop_price'];
            
            $spec_key = '';
            $spec_key_name = '';
            if (! empty($item_id)) {
                $specgoods = SpecGoodsPrice::getbyitemsid($item_id, 
                        $good['goods_id']);
                if (! empty($specgoods)) {
                    $store_count = $specgoods['store_count'];
                    $goodsprice = $specgoods['price'];
                    $spec_key = $specgoods['key'];
                    $spec_key_name = $specgoods['key_name'];
                } else {
                    return $this->jsonFail([], '请检查商品属性是否对应');
                }
            }
            if ($store_count < $goods_num) {
                return $this->jsonFail($goods, $goods['goods_name'] . '没有足够的库存');
            }
            $theprice += $goodsprice * $goods_num;
            if ($goods['is_free_shipping'] != Goods::FREE_SHIPPING) {
                $theweight += $goods['weight'];
            }
        }
        
        // 收货地址
        $address_id = Yii::$app->request->post('address_id');
        if (empty($address_id)) {
            $address = Address::find()->where(
                    'user_id=:user_id and is_default = :is_default', 
                    [
                            ':user_id' => $user_id,
                            ':is_default' => Address::STATUS_IS_DEFAULT
                    ])
                ->asArray()
                ->one();
        } else {
            $address = Address::find()->where('address_id=:address_id', 
                    [
                            ':address_id' => $address_id
                    ])->one();
        }
        
        if (! $address) {
            return $this->jsonFail([], '没有找到地址');
        }
        
        $shipping_price = 0;
        $shipping_code = Yii::$app->request->post('shipping');
        $areashippings = AreaRegion::find()->where('region_id = :region_id', 
                [
                        ':region_id' => $address['province']
                ])->all();
        if (empty($areashippings)) {
            $shipping = ShippingArea::find()->select('config')
                ->where('shipping_code = :shipping_code and is_default = 1', 
                    [
                            ':shipping_code' => $shipping_code
                    ])
                ->asArray()
                ->one();
        } else {
            $shipping = ShippingArea::find()->select('config')
                ->where('shipping_code = :shipping_code', 
                    [
                            ':shipping_code' => $shipping_code
                    ])
                ->andWhere(
                    'shipping_area_id in ' . $areashippings['shipping_area_id'])
                ->asArray()
                ->one();
        }
        // 反序列化邮费模板
        $the_config = unserialize($shipping['config']);
        if ($theweight <= $the_config['first_weight']) {
            $shipping_price = $the_config['money'];
        } else {
            $shipping_price += $the_config['money'];
            $goods_weight -= $the_config['first_weight'];
            $thecount = ceil($theweight / $the_config['second_weight']);
            $shipping_price += $the_config['add_money'] * $thecount;
        }
        
        // 订单
        $order = new Order();
        $order->user_id = 1;
        $order->pay_name = Yii::$app->request->post('pay_name');
        $order->shipping_price = $shipping_price;
        $order->goods_price = $theprice; // 订单总价
        $order->order_sn = $StringUtile->genSerialNum();
        if (empty($order->pay_name)) {
            return $this->jsonFail([], '请选择支付方式');
        }
        
        $order->province = $address['province'];
        $order->city = $address['city'];
        $order->district = $address['district'];
        $order->twon = $address['twon'];
        $order->address = $address['address'];
        $order->consignee = $address['consignee'];
        $order->mobile = $address['mobile'];
        $order->add_time = time();
        if (! ($order->load($order, '') && $order->save())) {
            return $this->jsonFail([], '保存订单失败');
        } else {
            // 订单包含商品
            $order_good[] = [];
            foreach ($cars as $good) {
                $order_goods = new OrderGoods();
                $order_goods->order_id = $order->order_id;
                $order_goods->goods_id = $good['goods_id'];
                $order_goods->goods_num = $good['goods_num'];
                if (! empty($good['specgoods_id'])) {
                    $item_id = $good['specgoods_id'];
                }
                $goods = Goods::find()->select(
                        'goods_name,goods_sn,market_price')
                    ->where('goods_id=:goods_id', 
                        [
                                'goods_id' => $good['goods_id']
                        ])
                    ->one();
                $order_goods->goods_name = $goods['goods_name'];
                $order_goods->goods_sn = $goods['goods_sn'];
                $order_goods->market_price = $goods['market_price'];
                $spec_key = '';
                $spec_key_name = '';
                if (! empty($item_id)) {
                    $specgoods = SpecGoodsPrice::getbyitemsid($item_id, 
                            $good['goods_id']);
                    $goodsprice = $specgoods['price'];
                    $spec_key = $specgoods['key'];
                    $spec_key_name = $specgoods['key_name'];
                    $specgoods->store_count = $specgoods['store_count'] -
                             $good['goods_num'];
                    if (! ($specgoods->save())) {
                        $order->deleted();
                        return $this->jsonFail([], '添加失败');
                    }
                }
                
                $thegoods = Goods_service::get_child_one_api($good['goods_id']);
                $thegoods->store_count = $thegoods->store_count -
                         $good['goods_num'];
                $thegoods->last_update = time();
                if (! ($thegoods->save())) {
                    $order->deleted();
                    return $this->jsonFail([], '添加失败');
                }
                
                $order_goods->goods_price = $goodsprice; // 商品价格
                $order_goods->spec_key = $spec_key;
                $order_goods->spec_key_name = $spec_key_name;
                if ($order_goods->save()) {
                    $order_good[] = [
                            'name' => $order_goods->goods_name,
                            'key' => $order_goods->spec_key_name,
                            'price' => $order_goods->goods_price,
                            'num' => $order_goods->goods_num
                    ];
                } else {
                    $order->deleted();
                    return $this->jsonFail([], '添加失败');
                }
            }
        }
        $order_action = new OrderAction();
        $order_action->order_id = $order->order_id;
        $order_action->log_time = time();
        $order_action->status_desc = "提交订单";
        if ($order->save() && $order_action->save()) {
            return $this->jsonSuccess(
                    [
                            $order,
                            $order_good,
                            $order_action
                    ], '创建订单成功');
        } else {
            $order->deleted();
            return $this->jsonFail([], '添加失败');
        }
    }

    // 付款//微信公众号付款
    public function actionPaywxjsapi ()
    {
        $order_id = Yii::$app->request->get('order_id');
        $order = Order::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])->one();
        if (empty($order)) {
            return $this->jsonFail([], '没有此订单');
        }
        if ($order['pay_status'] != 0) {
            return $this->jsonFail([], '已付款');
        }
        $order_sn = $order['order_sn'];
        $order_price = $order['goods_price']; // 商品价格
        $shipping_price = $order['shipping_price']; // 商品邮费
        $endprice = $order_price + $shipping_price; // 总付款
                                                    
        // 微信付款
        $tools = new JsApiPay();
        $openId = Yii::$app->session->get('openid');
        // ②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("小牛商城系统");
        $input->SetAttach("$order_sn"); // 附加数据 订单编码
        $input->SetOut_trade_no(
                WxPayConfig::MCHID . 'p' . $endprice . 'sn' . $order_sn);
        $input->SetTotal_fee("$endprice"); // 金额
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("公众号付款");
        $input->SetNotify_url(
                "http://api.wap.demo-xn.itdongli.com/v1/order/notifywxjsapi"); // 回调地址
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return json_encode($jsApiParameters);
    }

    // 付款回调
    public function actionNotifywxjsapi ()
    {
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', 
                LIBXML_NOCDATA);
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
        $order_id = $postObj->attach;
        $order = Order::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])->one();
        $order->pay_static = Order::PAY_STATUS_FINISH;
        
        $order_action = new OrderAction();
        $order_action->order_id = $order->order_id;
        $order_action->log_time = time();
        $order_action->status_desc = "微信付款";
        
        if (! ($order->save() && $order_action->save())) {
            return $this->jsonFail([], 'ERROR 请查询微信付款的记录');
        } else {
            return $this->jsonSuccess(
                    [
                            $order
                    ], '付款成功');
        }
    }

    // 确认收货
    public function actionConfirmgetgoods ()
    {
        $user_id = $this->getUserId();
        $order_id = Yii::$app->request->get('order_id');
        $order = Order::find()->where('order_id = :order_id ', 
                [
                        ':order_id' => $order_id
                ])->one();
        if (empty($order)) {
            return $this->jsonFail([], '没有此订单');
        }
        if ($order['order_status'] == Order::ORDER_STATUS_FINISH) {
            return $this->jsonFail([], '已确认收货');
        }
        if ($order['user_id'] != $user_id) {
            return $this->jsonFail([], '请登录正确的账户确认收货');
        }
        $order->order_status = Order::ORDER_STATUS_FINISH;
        
        $order_action = new OrderAction();
        $order_action->order_id = $order->order_id;
        $order_action->log_time = time();
        $order_action->status_desc = "确认收货";
        
        if (! ($order->save() && $order_action->save())) {
            return $this->jsonFail([], '收货失败');
        } else {
            return $this->jsonSuccess(
                    [
                            $order
                    ], '确认收货成功');
        }
    }
}
