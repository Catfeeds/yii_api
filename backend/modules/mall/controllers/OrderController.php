<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Order;
use backend\modules\mall\models\OrderAction;
use yii\data\Pagination;
use backend\modules\mall\models\OrderGoods;
use backend\modules\mall\models\DeliveryDoc;
use common\extensions\kdniao;
use backend\modules\mall\models\OrderSearch;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class OrderController extends BaseController
{

    public $modelClass = 'backend\modules\content\models\Order';

    // 查询所有订单
    public function actionIndex ()
    {
        
        $page_info = new Pagination(
                [
                        'totalCount' => Order::find()->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $orders = Order::find()->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        if ($orders == null) {
            return $this->jsonFail([], '未查询到');
        }
        return $this->jsonSuccess([
                $page_info,
                $orders
        ]);
    }

    // 查询订单货物
    public function actionIndexgoods ()
    {
        $order_id = Yii::$app->request->get('order_id');
        $page_info = new Pagination(
                [
                        'totalCount' => OrderGoods::find()->where(
                                'order_id=:order_id', 
                                [
                                        ':order_id' => $order_id
                                ])->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $goods = OrderGoods::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        if ($goods == null) {
            return $this->jsonFail([], '未查询到');
        }
        return $this->jsonSuccessWithPage($goods, $page_info);
    }

    // 查询订单的历史状态表
    public function actionIndexactions ()
    {
        
        $order_id = Yii::$app->request->get('order_id');
        $page_info = new Pagination(
                [
                        'totalCount' => OrderAction::find()->where(
                                'order_id=:order_id', 
                                [
                                        ':order_id' => $order_id
                                ])->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $actions = OrderAction::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        if ($actions == null) {
            return $this->jsonFail([], '未查询到');
        }
        return $this->jsonSuccessWithPage($actions, $page_info);
    }

    // 查询订单详情
    public function actionView ()
    {
        
        $order_id = $this->getParam('order_id');
        $order = Order::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])->one();
        return $this->jsonSuccess($order);
    }

    // 订单发货
    public function actionDeliveryorder ()
    {
        
        $order_id = Yii::$app->request->post('order_id');
        $invoice_no = Yii::$app->request->post('invoice_no');
        if (empty($invoice_no)) {
            return $this->jsonFail([], '请输入运单号');
        }
        $order = Order::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])->one();
        if (empty($order)) {
            return $this->jsonFail([], '没有此订单');
        }
        if ($order['shipping_status'] != 0) {
            return $this->jsonFail([], '已发货');
        }
        if ($order['pay_status'] != 1) {
            return $this->jsonFail([], '未付款');
        }
        $order->shipping_status = 1;
        $order->shipping_time = time();
        
        $order_action = new OrderAction();
        $order_action->order_id = $order->order_id;
        $order_action->log_time = time();
        $order_action->status_desc = "订单发货";
        
        $delivery_doc = new DeliveryDoc();
        $delivery_doc->load($order, '');
        $delivery_doc->invoice_no = $invoice_no;
        
        if (! ($order->save() || ! ($order_action->save()) ||
                 ! ($delivery_doc->validate() && $delivery_doc->save()))) {
            return $this->jsonFail([], '保存失败');
        } else {
            return $this->jsonSuccess(
                    [
                            $order
                    ], '发货成功');
        }
    }

    // 查询订单物流状态
    public function actionShowdelivery ()
    {
        
        $order_id = Yii::$app->request->get('order_id');
        $delivery = DeliveryDoc::find()->select('shipping_code,invoice_no')
            ->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])
            ->one();
        if (empty($delivery)) {
            return $this->jsonFail(
                    [
                            $delivery
                    ], '未查询到物流单号');
        }
        $kdniao = new kdniao();
        return $this->jsonSuccess(
                $kdniao->getOrderTracesByJson($delivery['shipping_code'], 
                        $delivery['invoice_no']), '成功');
    }

    public function actionSearch ()
    {
        $params = Yii::$app->request->post();
        if (empty($params)) {
            return $this->jsonFail([], '参数不可为空');
        }
        $ordersearch = new Order();
        $page_info = new Pagination(
                [
                        'totalCount' => $ordersearch->searchcount($params),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $orders = $ordersearch->search($params, $page_info->offset, 
                $page_info->limit);
        if (empty($orders)) {
            return $this->jsonFail([], '未查询到');
        }
        return $this->jsonSuccessWithPage($orders, $page_info);
    }
}