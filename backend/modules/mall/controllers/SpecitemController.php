<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\SpecItem;

class SpecitemController extends BaseController
{

    // 添加Specitem
    public function actionCreate ()
    {
        
        $data = Yii::$app->request->post();
        $specitem = new SpecItem();
        if ($specitem->load($data, '') && $specitem->validate()) {
            if ($specitem->save()) {
                return $this->jsonSuccess($specitem, '添加成功');
            } else {
                return $this->jsonFail([], '添加失败');
            }
        } else {
            return $this->jsonFail([], '参数不完整');
        }
    }

    // 修改Spec
    public function actionUpdate ()
    {
        
        $data = Yii::$app->request->post();
        $specitem = SpecItem::findByid($data['id']);
        if ($specitem->load($data, '') && $specitem->validate()) {
            if ($specitem->save()) {
                return $this->jsonSuccess($specitem, '修改成功');
            } else {
                return $this->jsonFail([], '修改失败');
            }
        } else {
            return $this->jsonFail([], '参数不完整');
        }
    }

    // 查看Specitem
    public function actionView ()
    {
       
        $id = Yii::$app->request->post('id');
        if (empty($id)) {
            return $this->jsonFail([], '请填写完整');
        }
        $specitem = SpecItem::findByid($id);
        return $this->jsonSuccess($specitem, '查询成功');
    }

    // 查询所有根据goods_id查询
    public function actionIndexbyspecid ()
    {
        
        $goods_id = Yii::$app->request->post('goods_id');
        if (empty($goods_id)) {
            return $this->jsonFail([], '请填写完整');
        }
        $specitems = SpecItem::findBygoodsid($goods_id);
        
        return $this->jsonSuccess($specitems, '查询成功');
    }
}
