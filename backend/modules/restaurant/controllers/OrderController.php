<?php
namespace backend\modules\restaurant\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\OrderService;
use yii\data\Pagination;
use backend\modules\restaurant\models\OrderFood;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderFoodAction;
use h5\modules\v1\service\printService;
use backend\modules\restaurant\models\OrderAddress;
use backend\modules\mall\models\Region;

class OrderController extends BaseController
{

    // 获取所有订单
    public function actionIndex()
    {
        $site_id = $this->getSite();
        $page_info = new Pagination([
            'totalCount' => OrderService::showbysitecount($site_id),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $offset = $page_info->offset;
        $limit = $page_info->limit;
        $orders = OrderService::showbysite($site_id, $offset, $limit);
        if (empty($orders)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccessWithPage($orders, $page_info);
        }
    }
    public function actionListbystatus()
    {
        $site_id = $this->getSite();
        $status = Yii::$app->request->get('status');
        $page_info = new Pagination([
            'totalCount' => OrderService::countbystatus($site_id,$status),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $offset = $page_info->offset;
        $limit = $page_info->limit;
        $orders = OrderService::listbystatus($site_id,$status, $offset, $limit);
        if (empty($orders)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccessWithPage($orders, $page_info);
        }
    }
    // 查看订单详情
    public function actionView()
    {
        $order_id = Yii::$app->request->get('order_id');
        $order = FoodOrder::findbyid($order_id);
        $orderfood = OrderService::allfoods($order_id);
        $orderaddress = OrderAddress::findOne(['order_sn'=>$order['order_sn']]);
        $address = Region::getRegions($orderaddress->twon);
        $address .= $orderaddress->address.' ';
        $address .= $orderaddress->consignee;
        $username = $orderaddress->consignee;
        $mobile = $orderaddress->mobile;
        if (! empty($order)) {
            return $this->jsonSuccess([
                'order' => $order,
                'order_food' => $orderfood,
                'address' => $address,
                'username' => $username,
                'mobile' => $mobile
            ], '查询成功');
        } else {
            return $this->jsonFail([], '查询失败');
        }
    }

    // 查看订单记录
    public function actionOrderaction()
    {
        $order_id = Yii::$app->request->get('order_id');
        $action = OrderFoodAction::showbyorder($order_id);
        if (empty($action)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($action, '查询完成');
        }
    }
    
    //代发货和待接单订单列表
    public function actionWaitintship()
    {
        $site_id = $this->getSite();
        $page_info = new Pagination([
            'totalCount' => OrderService::showwaitcount($site_id),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $offset = $page_info->offset;
        $limit = $page_info->limit;
        $orders = OrderService::showwait($site_id, $offset, $limit);
        if (empty($orders)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccessWithPage($orders, $page_info);
        }
    }
    public function actionPrint()
    {
        $order_sn = Yii::$app->request->get('order_sn');
        printService::OutAllWorkPrint($order_sn);
        return $this->jsonSuccess('', '打印成功');
    }
    // 订单发货
    public function actionShipping()
    {
        $order_id = Yii::$app->request->post('order_id');
        if (OrderService::shippingorder($order_id)) {
            return $this->jsonSuccess([], '成功');
        } else {
            return $this->jsonFail([], '失败');
        }
    }

    // 完成订单
    public function actionFinish()
    {
        $order_id = Yii::$app->request->post('order_id');
        if (OrderService::finishorder($order_id)) {
            return $this->jsonSuccess([], '成功');
        } else {
            return $this->jsonFail([], '失败');
        }
    }

    // 计划任务自动完成订单
    public function actionFinishautomatic()
    {
        if (OrderService::finishautomatic()) {
            return $this->jsonSuccess();
        } else {
            return $this->jsonFail();
        }
    }
    
    // 超时未付订单取消
    public function actionErrororder()
    {
        if (OrderService::EroorOrderAutomatic()) {
            return $this->jsonSuccess();
        } else {
            return $this->jsonFail();
        }
    }
}