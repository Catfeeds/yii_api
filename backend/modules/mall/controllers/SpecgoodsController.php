<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Spec;
use backend\modules\mall\models\SpecItem;
use backend\modules\mall\models\SpecGoodsPrice;
use backend\modules\mall\models\Goods;

class SpecgoodsController extends BaseController
{

    // 添加新的商品规格
    public function actionCreate ()
    {
        
        $data = Yii::$app->request->post();
        $keys = Yii::$app->request->post('keys');
        if (empty($keys)) {
            return $this->jsonFail([], 'keys不可为空');
        }
        sort($keys);
        $key = '';
        $key_name = '';
        foreach ($keys as $id) {
            $key .= '_' . $id;
            $specitem = SpecItem::findByid($id);
            $spec = Spec::findByid($specitem['spec_id']);
            $key_name .= $spec['name'] . ':' . $specitem['item'] . ' ';
        }
        $key = substr($key, 1);
        $specgoodprice = new SpecGoodsPrice();
        $specgoodprice->key = $key;
        $specgoodprice->key_name = $key_name;
        if ($specgoodprice->load($data, '') && $specgoodprice->validate()) {
            $goods = Goods::findbyid($data['goods_id']);
            $goods->store_count += $specgoodprice->store_count;
            if ($goods->shop_price > $specgoodprice->price ||
                     $goods->shop_price == 0) {
                $goods->shop_price = $specgoodprice->price;
            }
            if ($specgoodprice->save() && $goods->save()) {
                return $this->jsonSuccess($specgoodprice, '添加成功');
            } else {
                return $this->jsonFail([], '添加失败');
            }
        } else {
            return $this->jsonFail([], '参数不完整');
        }
    }

    // 修改商品规格
    public function actionUpdate ()
    {
        
        $data = Yii::$app->request->post();
        $specgoodprice = SpecGoodsPrice::findByid($data['item_id']);
        $keys = Yii::$app->request->post('keys');
        if (! empty($keys)) {
            sort($keys);
            $key = '';
            $key_name = '';
            foreach ($keys as $id) {
                $key .= '_' . $id;
                $specitem = SpecItem::findByid($id);
                $spec = Spec::findByid($specitem['spec_id']);
                $key_name .= $spec['name'] . ':' . $specitem['item'] . ' ';
            }
            $key = substr($key, 1);
            $specgoodprice->key = $key;
            $specgoodprice->key_name = $key_name;
        }
        if ($specgoodprice->load($data, '') && $specgoodprice->validate()) {
            if ($specgoodprice->save()) {
                return $this->jsonSuccess($specgoodprice, '修改成功');
            } else {
                return $this->jsonFail([], '修改失败');
            }
        } else {
            return $this->jsonFail([], '参数不完整');
        }
    }

    // 根据keys查询
    public function actionViewbykeys ()
    {
        
        $keys = Yii::$app->request->post('keys');
        if (empty($keys)) {
            return $this->jsonFail([], 'keys不可为空');
        }
        sort($keys);
        $key = '';
        foreach ($keys as $id) {
            $key .= '_' . $id;
        }
        $key = substr($key, 1);
        $specgoodprice = SpecGoodsPrice::findBykey($key);
        return $this->jsonSuccess($specgoodprice, '查询完成');
    }

    // 查询商品规格
    public function actionView ()
    {
        
        $id = Yii::$app->request->post('item_id');
        if (empty($id)) {
            return $this->jsonFail([], '查询失败');
        }
        $specgoodprice = SpecGoodsPrice::findByid($id);
        return $this->jsonSuccess($specgoodprice, '查询完成');
    }

    // 根据商品id返回所有的商品规格
    public function actionListgoods ()
    {
        
        $goods_id = Yii::$app->request->post('goods_id');
        if (empty($goods_id)) {
            return $this->jsonFail([], '参数不完整');
        }
        $specgoodprices = SpecGoodsPrice::getbygoodsid($goods_id);
        if (empty($specgoodprices)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($specgoodprices, '查询成功');
        }
    }

    // 删除商品规格价格
    public function actionDeleteone ()
    {
        
        $item_id = Yii::$app->request->get('item_id');
        if (empty($item_id)) {
            return $this->jsonFail([], '参数不完整');
        }
        $specgoodprice = SpecGoodsPrice::findByid($item_id);
        if ($specgoodprice->delete()) {
            return $this->jsonSuccess([], '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
}
