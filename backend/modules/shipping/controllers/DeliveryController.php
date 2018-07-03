<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use common\extensions\kdniao;
use backend\modules\shipping\models\DeliveryDoc;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class DeliveryController extends BaseController
{

    // 查询订单物流状态
    public function actionShowdelivery ()
    {
        $order_id = Yii::$app->request->get('order_id');
        $delivery = DeliveryDoc::find()->select('shipping_code,invoice_no')
            ->where('order_id=:order_id', [
                ':order_id' => $order_id
        ])
            ->one();
        if (empty($delivery)) {
            return $this->jsonFail([
                    $delivery
            ], '未查询到物流单号');
        }
        $kdniao = new kdniao();
        return $this->jsonSuccess(
                $kdniao->getOrderTracesByJson($delivery['shipping_code'], 
                        $delivery['invoice_no']), '成功');
    }

    // 根据物流code和物流单号查询
    public function actionFind ()
    {
        $code = Yii::$app->request->get('code');
        $invoice_no = Yii::$app->request->get('invoice_no');
        if (empty($invoice_no) || empty($code)) {
            return $this->jsonFail([], '参数不完整');
        }
        $kdniao = new kdniao();
        $data = $kdniao->getOrderTracesByJson($code, $invoice_no);
        if (empty($data)) {
            return $this->jsonFail([], '查询失败');
        }
        return $this->jsonSuccess($data, '成功');
    }
}