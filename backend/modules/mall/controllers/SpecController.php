<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Spec;

class SpecController extends BaseController
{

    // 添加Spec
    public function actionCreate ()
    {
       
        $data = Yii::$app->request->post();
        $spec = new Spec();
        if ($spec->load($data, '') && $spec->validate()) {
            if ($spec->save()) {
                return $this->jsonSuccess($spec, '添加成功');
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
        $spec = Spec::findByid($data['id']);
        if ($spec->load($data, '') && $spec->validate()) {
            if ($spec->save()) {
                return $this->jsonSuccess($spec, '修改成功');
            } else {
                return $this->jsonFail([], '修改失败');
            }
        } else {
            return $this->jsonFail([], '参数不完整');
        }
    }

    // 查看Spec
    public function actionView ()
    {
        
        $id = Yii::$app->request->post('id');
        if (empty($id)) {
            return $this->jsonFail([], '请填写完整');
        }
        $spec = Spec::find()->where('id=:id', [
                ':id' => $id
        ])->one();
        return $this->jsonSuccess($spec, '查询成功');
    }

    // 查询所有Spec
    public function actionIndex ()
    {
        $specs = Spec::find()->all();
        return $this->jsonSuccess($specs, '查询成功');
    }
}
