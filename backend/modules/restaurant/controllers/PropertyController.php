<?php
namespace backend\modules\restaurant\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\PropertyService;

class PropertyController extends BaseController
{

    // 获取菜品的所有属性
    public function actionIndex ()
    {
        $food_id = Yii::$app->request->get('food_id');
        if(empty($food_id)){
            return $this->jsonFail([],'参数缺失');
        }
        $foods = PropertyService::showbyfood($food_id);
        if (empty($foods)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($foods, '查询成功');
        }
    }

    // 获取具体内容
    public function actionIndexbyid ()
    {
        $pro_id = Yii::$app->request->get('property_id');
        if(empty($pro_id)){
            return $this->jsonFail([],'参数缺失');
        }
        $pro = PropertyService::showbyid($pro_id);
        if (empty($pro)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($pro, '查询成功');
        }
    }

    // 创建菜品属性
    public function actionCreat ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        $child = Yii::$app->request->post('child');
        if (!empty($model = PropertyService::createwithchild($data, $child))) {
            return $this->jsonSuccess($model, '创建成功');
        } else {
            return $this->jsonFail([], '创建失败');
        }
    }

    // 修改菜品属性
    public function actionUpdate ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if (empty($data['food_id'])) {
            return $this->jsonFail([], '参数缺失');
        }
        if (!empty($model = PropertyService::update($data))) {
            return $this->jsonSuccess($model, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }

    // 修改菜品具体属性
    public function actionUpdatechild ()
    {
        
        $user_id = $this->getUserId();
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if (empty($data['property_id'])) {
            return $this->jsonFail([], '参数缺失');
        }
        if (!empty($model = PropertyService::updatechild($data,$user_id))) {
            return $this->jsonSuccess($model, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }

    // 删除菜品的属性
    public function actionDelete ()
    {
       
        // 这里要判断是否为本店铺管理员。
        $food_id = Yii::$app->request->post('property_id');
        if(empty($food_id)){
            return $this->jsonFail([],'参数缺失');
        }
        if (PropertyService::delete($food_id)) {
            return $this->jsonSuccess([], '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
    // 删除菜品的属性的值
    public function actionDeletechild ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $child_id= Yii::$app->request->post('child_id');
        if(empty($child_id)){
            return $this->jsonFail([],'参数缺失');
        }
        if (PropertyService::deletechild($child_id)) {
            return $this->jsonSuccess([], '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
}