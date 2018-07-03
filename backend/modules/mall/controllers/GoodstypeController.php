<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\GoodsType;

class GoodstypeController extends BaseController
{

    // 显示GoodsType
    public function actionIndex ()
    {
        $goods_type = GoodsType::find()->all();
        return $this->jsonSuccess($goods_type, '查询成功');
    }

    // 添加GoodsType
    public function actionCreate ()
    {
        $name = Yii::$app->request->post('name');
        if (empty($name) || empty($id)) {
            return $this->jsonFail([], '请填写完整');
        }
        $goods_type = new GoodsType();
        $goods_type->name = $name;
        if ($goods_type->save()) {
            return $this->jsonSuccess($goods_type, '添加成功');
        } else {
            return $this->jsonFail([], '添加失败');
        }
    }

    // 修改
    public function actionUpdate ()
    {
        $name = Yii::$app->request->post('name');
        $id = Yii::$app->request->post('type_id');
        if (empty($name) || empty($id)) {
            return $this->jsonFail([], '请填写完整');
        }
        $goods_type = GoodsType::find()->where('id=:id', [
                ':id' => $id
        ])->one();
        $goods_type->id = $id;
        $goods_type->name = $name;
        if ($goods_type->save()) {
            return $this->jsonSuccess($goods_type, '添加成功');
        } else {
            return $this->jsonFail([], '添加失败');
        }
    }
}
