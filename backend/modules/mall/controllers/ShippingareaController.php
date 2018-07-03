<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Shipping;
use backend\modules\mall\models\ShippingArea;
use backend\modules\mall\models\AreaRegion;
use backend\modules\mall\models\Region;

class ShippingareaController extends BaseController
{

    // 显示所有的物流模板
    public function actionListall ()
    {
       
        $shippingareas = ShippingArea::find()->all();
        if (empty($shippingareas)) {
            return $this->jsonFail([], '未查询到物流模板');
        }
        $rows = array();
        foreach ($shippingareas as $i => $shippingarea) {
            $shippingarea->config = unserialize($shippingarea['config']);
            $rows[$i] = $shippingarea->attributes;
            $rows[$i]['region'] = AreaRegion::findbyshipid(
                    $shippingarea['shipping_area_id']);
        }
        return $this->jsonSuccess($rows, '查询成功');
    }

    // 根据物流公司显示所有物流模板表
    public function actionIndex ()
    {
       
        $shipping_code = Yii::$app->request->get('shipping_code');
        $shippingareas = ShippingArea::find()->where(
                'shipping_code=:shipping_code', 
                [
                        'shipping_code' => $shipping_code
                ])->all();
        if (empty($shippingareas)) {
            return $this->jsonFail([], '未查询到物流模板');
        }
        foreach ($shippingareas as $shippingarea) {
            $shippingarea->config = unserialize($shippingarea['config']);
        }
        return $this->jsonSuccess($shippingareas, '查询成功');
    }

    // 物流公司code查看物流公司具体信息
    public function actionView ()
    {
        
        $id = Yii::$app->request->get('id');
        $shipping_area = ShippingArea::find()->where(
                'shipping_area_id=:shipping_area_id', 
                [
                        ':shipping_area_id' => $id
                ])->one();
        if (empty($shippingarea)) {
            return $this->jsonFail([], '未查询到物流模板');
        }
        return $this->jsonSuccess($shipping_area, '查询成功');
    }

    // 创建新的物流模板
    public function actionCreate ()
    {
        
        $shipping_code = Yii::$app->request->post('shipping_code');
        $shipping_area_name = Yii::$app->request->post('shipping_area_name');
        $shipping_area = new ShippingArea();
        $first_weight = Yii::$app->request->post('first_weight');
        $money = Yii::$app->request->post('money');
        $second_weight = Yii::$app->request->post('second_weight');
        $add_money = Yii::$app->request->post('add_money');
        // 如何序列化？
        $array_config = [
                'first_weight' => $first_weight,
                'money' => $money,
                'second_weight' => $second_weight,
                'add_money' => $add_money
        ];
        
        $config = serialize($array_config);
        
        $shipping_area->shipping_area_name = $shipping_area_name;
        $shipping_area->shipping_code = $shipping_code;
        $shipping_area->config = $config;
        $shipping_area->update_time = time();
        
        if (! $shipping_area->save()) {
            return array_values($shipping_area->getFirstErrors())[0];
        }
        // 和地区表相关联
        $the_region_id = $shipping_area->shipping_area_id;
        $regions = Yii::$app->request->post('regions');
        foreach ($regions as $id) {
            $areaRegion = new AreaRegion();
            $areaRegion->shipping_area_id = $id;
            $areaRegion->region_id = $the_region_id;
            $areaRegion->save();
        }
        return $this->jsonSuccess($shipping_area, '添加成功');
    }

    // 后台添加物流公司信息//默认开启
    public function actionAddshipping ()
    {
       
        $shipping = new Shipping();
        $shipping->shipping_code = Yii::$app->request->post('shipping_code');
        $shipping->shipping_name = Yii::$app->request->post('shipping_name');
        $shipping->shipping_desc = Yii::$app->request->post('shipping_desc');
        $shipping->insure = Yii::$app->request->post('shipping_insure');
        if (! $shipping->save()) {
            return array_values($shipping->getFirstErrors())[0];
        }
        return $this->jsonSuccess($shipping);
    }
}
