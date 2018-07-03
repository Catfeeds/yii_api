<?php
namespace backend\modules\restaurant\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderFoodAction;
use yii\data\Pagination;
use backend\modules\restaurant\models\FoodOrderRefund;
use backend\modules\restaurant\service\FoodOrderService;
use backend\modules\restaurant\models\WxRefund;
use backend\modules\wechat\service\wechat;
use common\extensions\Wechat\WechatPay;

class RefundController extends BaseController
{    
    //显示待处理后的退货订单。
    public function actionShowreend()
    {
        $site_id = $this->getSite();
        $page_info = new Pagination ( [
            'totalCount' => FoodOrderService::countdo( $site_id),
            'defaultPageSize' => empty ( $this->getParam ( 'per-page' ) ) ? '10' : $this->getParam ( 'per-page' )
        ]);
        $page_info->setPage ( $this->getParam ( 'page' ) - 1 );
        $foodorder = FoodOrderService::showdo( $site_id, $page_info->offset, $page_info->limit );
        return $this->jsonSuccessWithPage ( $foodorder, $page_info );
    }
    //显示待处理中的退货订单。
    public function actionShowrefund()
    {
        $site_id = $this->getSite();
        $page_info = new Pagination ( [
            'totalCount' => FoodOrderService::countorder( $site_id,FoodOrder::ORDER_STATUS_REDUNDS),
            'defaultPageSize' => empty ( $this->getParam ( 'per-page' ) ) ? '10' : $this->getParam ( 'per-page' )
        ]);
        $page_info->setPage ( $this->getParam ( 'page' ) - 1 );
        $foodorder = FoodOrderService::showorder( $site_id,FoodOrder::ORDER_STATUS_REDUNDS, $page_info->offset, $page_info->limit );
        return $this->jsonSuccessWithPage ( $foodorder, $page_info );
    }
    //退货订单详情
    public function actionShowone()
    {
        $order_id = Yii::$app->request->get('order_id');
        $site_id = $this->getSite();
        $order = FoodOrderService::showRefund($order_id, $site_id);
        if(empty($order)){
            return $this->jsonFail('','未查询到此订单/此订单没有退货记录');
        }else{
            return $this->jsonSuccess($order,'查询成功');
        }
    }
    public function actionDorefund()
    {
        $msg = '';
        $user_id = $this->getUserId();
        $order_id = Yii::$app->request->post('order_id');
        $site_id = $this->getSite();
        $note = Yii::$app->request->post('note');
        $result = Yii::$app->request->post('result');
        
        $order = FoodOrder::findOne(['order_id'=>$order_id,'site_id'=>$site_id]);
        $count = FoodOrderRefund::find()->where(['order_id'=>$order_id])->count();
        if($count == 0 || empty($order)){
            return $this->jsonFail('','未查询到此订单/此订单没有退货记录');
        }
        
        $refund = new FoodOrderRefund();
        $refund->order_id = $order_id;
        $refund->order_status = $order['order_status'];
        $refund->admin_id = $this->getUserId();
        $action = new OrderFoodAction();
        $action->site_id = $site_id;
        $action->order_id = $order_id;
        
        if($result == 1){
            $action->order_status = FoodOrder::ORDER_STATUS_AGREE_REFUNDS;
            $action->order_desc = OrderFoodAction::STATUS_DESC_AGREE;
            $order->order_status= FoodOrder::ORDER_STATUS_AGREE_REFUNDS;
            
            $refund->status = FoodOrderRefund::STATUS_AGREE;
            /*
             * 其他退货流程
             * 退款。
             */
            $wx_refund = WxRefund::findOne(['order_sn'=>$order->order_sn]);
            if(empty($wx_refund)){
                $wx_refund = new WxRefund();
                $wx_refund->order_sn = $order->order_sn;
                $wx_refund->other_sn = $order->other_sn;
                $wx_refund->refund_no = $site_id.$user_id.time() . mt_rand(1000, 9999);
                $wx_refund->order_price = ($order->order_price+$order->box_price+$order->shipping_price)*100;
                $wx_refund->refund_price = ($order->order_price+$order->box_price+$order->shipping_price)*100;
                $wx_refund->user_id = $user_id;
                $wx_refund->create_at = time();
                $wx_refund->save();
            }
            $wechatconfig = wechat::getconfig($site_id);
            $pay = new WechatPay($wechatconfig);
            $results = $pay->refund($wx_refund->order_sn, $wx_refund->other_sn, $wx_refund->refund_no, $wx_refund->order_price, $wx_refund->refund_price, $op_user_id = null);
            if($results===FALSE){
                // 接口失败的处理
                $msg = $pay->errMsg;
            }elseif($results['']){
                // 接口成功的处理
                $wx_refund->status = 1;
                $order->order_status = FoodOrder::ORDER_STATUS_REFUNDS_END;
            }
            
        }else{
            $action->order_status = FoodOrder::ORDER_STATUS_DISAGREE_REFUNDS;
            $action->order_desc = OrderFoodAction::STATUS_DESC_DISAGREE;
            $order->order_status = FoodOrder::ORDER_STATUS_DISAGREE_REFUNDS;
            $refund->status = FoodOrderRefund::STATUS_DISAGREE;
        }
        $action->log_time = time();
        $action->action_note = $note;
        $action->save();
        $refund->create_at = time();
        $refund->note = $note;
        /*
         * 通知用户退货情况
         */
        if(empty($msg)){
            if($order->save()&&$refund->save()){
                return $this->jsonSuccess('','处理成功');
            }else{
                return $this->jsonFail('','处理失败');
            }
        }else{
            return $this->jsonFail($msg,'处理失败');
        }
    }
    
    public function actionShowrefundend()
    {
        $site_id = $this->getSite();
        $page_info = new Pagination ( [
            'totalCount' => FoodOrderService::countorder( $site_id,FoodOrder::ORDER_STATUS_REDUNDS),
            'defaultPageSize' => empty ( $this->getParam ( 'per-page' ) ) ? '10' : $this->getParam ( 'per-page' )
        ]);
        $page_info->setPage ( $this->getParam ( 'page' ) - 1 );
        $foodorder = FoodOrderService::showorder( $site_id,FoodOrder::ORDER_STATUS_REDUNDS, $page_info->offset, $page_info->limit );
        return $this->jsonSuccessWithPage ( $foodorder, $page_info );
    }
    
	//商家审批
	public function actionApproval(){
		$data = Yii::$app->request->post();
		$food_order= FoodOrder::findbyid($data['order_id']);
		//1：同意
		if($data['result'] == '1'){
			//判断货物是否已经配送，通知外卖员处理
			// if(){
			
			// }
			
			//food_order表
			$food_order->order_status = FoodOrder::ORDER_STATUS_REDUNDS;
			$food_order->update();
			
			//order_food_action表
			$order_food_action= new OrderFoodAction();
			$data['order_status']= FoodOrder::ORDER_STATUS_REDUNDS;
			$data['action_note']= OrderFoodAction::STATUS_DESC_AGREE;
			$data['order_desc'] = OrderFoodAction::STATUS_DESC_AGREE;
			$data['log_time'] = time();
			
			if ($order_food_action->load ( $data, '' ) && $order_food_action->save ()) {
				return $this->jsonSuccess([], '创建订单记录成功!');
			}else {
				return $this->jsonFail([], '创建订单记录失败!');
			}
			
			//同意退货和退货通知待写
			
		}
		if($data['result'] == '2'){
			//food_order表
			$food_order->order_status = FoodOrder::ORDER_STATUS_DISAGREE;
			$food_order->update();
			
			//order_food_action表
			$order_food_action = new OrderFoodAction();
			$data['order_status'] = FoodOrder::ORDER_STATUS_DISAGREE;
			$data['action_note'] = OrderFoodAction::STATUS_DESC_DISAGREE;
			$data['order_desc'] = OrderFoodAction::STATUS_DESC_DISAGREE;
			$data['log_time'] = time();
			if ($order_food_action->load ( $data, '' ) && $order_food_action->save ()) {
				return $this->jsonSuccess([], '创建订单记录成功!');
			}else {
				return $this->jsonFail([], '创建订单记录失败!');
			}
			
			//同意退货和退货通知待写
		}	
	}
	
	public function actionRefundindex(){
		$site_id = $this->getSite ();
		if(empty($site_id)){
			return $this->jsonFail([], '未输入站点!');
		}
		$page_info = new Pagination ( [
				'totalCount' => FoodOrderService::showbyapplycount ( $site_id ),
				'defaultPageSize' => empty ( $this->getParam ( 'per-page' ) ) ? '10' : $this->getParam ( 'per-page' )
		] );
		$page_info->setPage ( $this->getParam ( 'page' ) - 1 );
		$foodorder = FoodOrderService::showbyapply( $site_id, $page_info->offset, $page_info->limit );
		return $this->jsonSuccessWithPage ( $foodorder, $page_info );
	}
	
	public function actionView(){
		$order_id = Yii::$app->request->get('order_id');
		if(empty($order_id)){
			return $this->jsonFail([], '参数不完整！');
		}
		$food_order = FoodOrder::findbyid($order_id);
		if(empty($food_order)){
			return $this->jsonFail([],'未查询到订单！');
		}
		return $this->jsonSuccess($food_order, '查询成功！');
	}
}