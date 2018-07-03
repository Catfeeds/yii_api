<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\DeliveryDoc;
use yii\data\Pagination;

class DeliveryController extends BaseController
{

    // 显示所有发货单
    public function actionIndex ()
    {
        $page_info = new Pagination(
                [
                        'totalCount' => DeliveryDoc::find()->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $deliverydocs = DeliveryDoc::find()->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        return $this->jsonSuccessWithPage($deliverydocs, $page_info);
    }

    // 根据用户id显示发货单
    public function actionShowbyuserid ()
    {
        $user_id = Yii::$app->request->get('user_id');
        $page_info = new Pagination(
                [
                        'totalCount' => DeliveryDoc::find()->where(
                                'user_id=:user_id', [
                                        ':user_id' => $user_id
                                ])->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $deliverydocs = DeliveryDoc::find()->where('user_id=:user_id', 
                [
                        ':user_id' => $user_id
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        return $this->jsonSuccessWithPage($deliverydocs, $page_info);
    }

    // 根据物流code显示发货单
    public function actionShowbyshipping ()
    {
        $code = Yii::$app->request->get('code');
        $page_info = new Pagination(
                [
                        'totalCount' => DeliveryDoc::find()->where(
                                'shipping_code=:code', [
                                        ':code' => $code
                                ])->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $deliverydocs = DeliveryDoc::find()->where('shipping_code=:code', 
                [
                        ':code' => $code
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        return $this->jsonSuccessWithPage($deliverydocs, $page_info);
    }

    // 根据order_id显示具体发货单
    public function actionShowbyorder ()
    {
        $order_id = Yii::$app->request->get('order_id');
        $deliverydoc = DeliveryDoc::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $order_id
                ])->one();
        return $this->jsonSuccess($deliverydoc, '查询成功');
    }

    // 根据id显示具体发货单
    public function actionShowbyid ()
    {
        $id = Yii::$app->request->get('id');
        $deliverydoc = DeliveryDoc::find()->where('id=:id', [
                ':id' => $id
        ])->one();
        return $this->jsonSuccess($deliverydoc, '查询成功');
    }

    // 根据物流单号查询
    public function actionShowbyinvoice ()
    {
        $invoice = Yii::$app->request->get('invoice');
        $deliverydoc = DeliveryDoc::find()->where('invoice_no=:invoice_no', 
                [
                        ':invoice_no' => $invoice
                ])->one();
        return $this->jsonSuccess($deliverydoc, '查询成功');
    }
}
