<?php

/**
 * @author Jason
 * @date 2016-08-08
 * @copyright Copyright © 2016年 EleTeam
 */

namespace api\modules\v1\controllers;

use api\base\BaseController;
use Yii;
use api\modules\v1\service\FoodOrderService;
use yii\data\Pagination;
use api\modules\v1\service\FoodCarService;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderFood;
use common\extensions\wxpay\example\JsApiPay;
use common\extensions\wxpay\lib\WxPayUnifiedOrder;
use common\extensions\wxpay\lib\WxPayApi;
use backend\modules\restaurant\models\OrderFoodAction;

/**
 * 订单控制器
 * Class OrderController
 *
 * @package api\modules\v1\controllers
 */
class FoodorderController extends BaseController
{

    public function actionCreateorder()
    {
        $user_id = $this->getUserId();
        $site_id = $this->getSite();
        $table_id = Yii::$app->request->post('table_id');
        $car = FoodCarService::showall($table_id);   //桌号查询出的所有数据
        if (empty($site_id)) {
            return $this->jsonFail([], '店铺未选中');
        }
        if (!empty($model = FoodOrderService::addorder($user_id, $site_id, $table_id, $car))) {
            FoodCarService::clearcar($table_id);
            return $this->jsonSuccess($model, '成功');
        } else {
            return $this->jsonFail([], '点餐失败');
        }
    }

    // 展示我的订单
    public function actionShoworder()
    {
        $user_id = $this->getUser();
        $page_info = new Pagination([
            'totalCount' => FoodOrderService::showmyordercount($user_id),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '5' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        if (! empty($model = FoodOrderService::showmyorderwithpage($user_id, $page_info->offset, $page_info->limit))) {
            return $this->jsonSuccessWithPage($model,$page_info, '查询成功');
        } else {
            return $this->jsonFail([], '查询失败');
        }
    }

    // 店铺内我的订单
    public function actionShowsiteorder()
    {
        $user_id = $this->getUser();
        $site_id = $this->getSite();
        $page_info = new Pagination([
            'totalCount' => FoodOrderService::showmyordercount($user_id, $site_id),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        if (! empty($model = FoodOrderService::showmyorderwithpage($user_id, $page_info->offset, $page_info->limit, $site_id))) {
            return $this->jsonSuccess($model, '查询成功');
        } else {
            return $this->jsonFail([], '查询失败');
        }
    }

    // 展示未完成的订单
    public function actionShownotpay()
    {
        $user_id = $this->getUser();
        $site_id = $this->getSite();
        $table = Yii::$app->request->get('table_id');
        if (! empty($model = FoodOrderService::shownotpay($user_id, $table, $site_id))) {
            return $this->jsonSuccess($model, '查询成功');
        } else {
            return $this->jsonFail([], '查询失败');
        }
    }

    // 付款接口
    public function actionPayorder()
    {
        $user_id = $this->getUser();
        $openId = $this->getOpenId();
        $order_sn = Yii::$app->request->post('order_sn');
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
        
        $tools = new JsApiPay();
        // ②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("小牛");
        $input->SetAttach($order['order_id']); // 附加数据
        $input->SetOut_trade_no($order_sn);
        $input->SetDevice_info('test');
        $input->SetTotal_fee("$price"); // 金额
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("https://api.m.demo-xn.itdongli.com/v1/foodorder/paynotify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        // echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        $jsApiParameters = json_decode($tools->GetJsApiParameters($order), 1);
        return $this->jsonSuccess($jsApiParameters);
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
        $id = $postObj->attach;
        $model = FoodOrder::findOne([
            'order_id' => $id
        ]);
        $model->order_status = FoodOrder::ORDER_STATUS_FINISH;
        
        $action = new OrderFoodAction();
        $action->order_id = $id;
        $action->order_status = FoodOrder::ORDER_STATUS_PAY;
        $action->order_desc = OrderFoodAction::ACTION_NOTE_PAY;
        $action->log_time = time();
        $action->save();
        $model->save();
        return $model->save(false) ? SUCCESS : OK;
    }

    public function actionShowtheorder()
    {
        $user_id = $this->getUser();
        $order_sn = Yii::$app->request->post('order_sn');
        $order = FoodOrder::findOne([
            'order_sn' => $order_sn
        ]);
        $foods = OrderFood::findAll([
            'order_id' => $order['order_id']
        ]);
        if (! empty($order) && ! empty($foods)) {
            return $this->jsonSuccess([
                'order' => $order,
                'foods' => $foods
            ], '查询成功');
        } else {
            return $this->jsonFail([], '未查询到');
        }
    }

    public function getLocalIP()
    {
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        
        exec("ifconfig", $out, $stats);
        if (! empty($out)) {
            if (isset($out[1]) && strstr($out[1], 'addr:')) {
                $tmpArray = explode(":", $out[1]);
                $tmpIp = explode(" ", $tmpArray[1]);
                if (preg_match($preg, trim($tmpIp[0]))) {
                    return trim($tmpIp[0]);
                }
            }
        }
        return '127.0.0.1';
    }
    
    //申请退款
    public function actionApplyrefund(){
    	$user = $this->getUserId();
    	$data = Yii::$app->request->post();
    	$food_order= FoodOrder::findbyid($data['order_id']);
    	//提交给商家处理
    	if($food_order->order_status == FoodOrder::ORDER_STATUS_FINISH || $food_order->order_status == FoodOrder::ORDER_STATUS_PAY ){
    		//food_order表
    		$food_order->order_status = FoodOrder::ORDER_STATUS_APPLY_REDUNDS;
    		$food_order->update();
    		
    		//order_food_action表
    		$order_food_action = new OrderFoodAction();
    		$data['order_status'] = FoodOrder::ORDER_STATUS_APPLY_REDUNDS;
    		$data['action_note'] = OrderFoodAction::STATUS_DESC_APPLY_REDUNDS;
    		$data['order_desc'] = OrderFoodAction::STATUS_DESC_APPLY_REDUNDS;
    		$data['log_time'] = time();
    		if ($order_food_action->load ( $data, '' ) && $order_food_action->save ()) {
    			return $this->jsonSuccess([], '创建订单记录成功!');
    		}else {
    			return $this->jsonFail([], '创建订单记录失败!');
    		}
    		
    	}
    	//提交订单状态的订单直接退货
    	if($food_order->order_status == FoodOrder::ORDER_STATUS_CREATE ){
    		//food_order表
    		$food_order->order_status = FoodOrder::ORDER_STATUS_REDUNDS;
    		$food_order->update();
    		
    		//order_food_action表
    		$order_food_action = new OrderFoodAction();
    		$data['order_status'] = FoodOrder::ORDER_STATUS_REDUNDS;
    		$data['action_note'] = OrderFoodAction::STATUS_DESC_AGREE;
    		$data['order_desc'] = OrderFoodAction::STATUS_DESC_AGREE;
    		$data['log_time'] = time();
    		if ($order_food_action ->load ( $data, '' ) && $order_food_action->save ()) {
    			return $this->jsonSuccess([], '创建订单记录成功!');
    		}else {
    			return $this->jsonFail([], '创建订单记录失败!');
    		}
    		
    		
    		//退货和退货通知待写
    		
    	}
    }
}
