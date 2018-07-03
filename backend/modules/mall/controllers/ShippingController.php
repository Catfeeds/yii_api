<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Shipping;

class ShippingController extends BaseController
{

    // 显示所有物流公司
    public function actionIndex ()
    {
        $shippings = Shipping::find()->all();
        
        return $this->jsonSuccess($shippings, '查询成功');
    }

    // 物流公司code查看物流公司具体信息
    public function actionView ()
    {
        $shipping_code = Yii::$app->request->get('code');
        $shipping = Shipping::find()->where('shipping_code=:shipping_code', 
                [
                        ':shipping_code' => $shipping_code
                ])->one();
        if (! empty($shipping)) {
            return $this->jsonSuccess($shipping, '查询成功');
        } else {
            return $this->jsonFail([], '未查询到');
        }
    }

    // 后台添加物流公司信息//默认开启
    public function actionAddshipping ()
    {
        $code = Yii::$app->request->post('shipping_code');
        $name = Yii::$app->request->post('shipping_name');
        $desc = Yii::$app->request->post('shipping_desc');
        $insure = Yii::$app->request->post('shipping_insure');
        if (empty($code) || empty($name)) {
            return $this->jsonFail([
                    Yii::$app->request->post()
            ], '参数不完整');
        }
        $shipping = new Shipping();
        $shipping->shipping_code = $code;
        $shipping->shipping_name = $name;
        $shipping->shipping_desc = $desc;
        $shipping->insure = $insure;
        if (! $shipping->save()) {
            return array_values($shipping->getFirstErrors())[0];
        }
        return $this->jsonSuccess($shipping);
    }
}
