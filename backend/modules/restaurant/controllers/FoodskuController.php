<?php
namespace backend\modules\restaurant\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\TablesService;
use backend\modules\restaurant\service\FoodSKUService;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class FoodskuController extends BaseController
{

    // 获取菜品的所有sku
    public function actionIndex ()
    {
        $food_id = Yii::$app->request->get('food_id');
        $foods = FoodSKUService::showbyfood($food_id);
        if (empty($foods)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($foods, '查询成功');
        }
    }

    // 获取sku具体内容
    public function actionIndexbyid ()
    {
        $id = Yii::$app->request->get('id');
        $foodsku = FoodSKUService::showbyid($id);
        if (empty($foodsku)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($foodsku, '查询成功');
        }
    }

    // 创建菜品SKU
    public function actionCreat ()
    {
       
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if (!empty($model = FoodSKUService::create($data))) {
            return $this->jsonSuccess($model, '创建成功');
        } else {
            return $this->jsonFail([], '创建失败');
        }
    }

    // 修改菜品SKU
    public function actionUpdate ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if (empty($data['food_id'])) {
            return $this->jsonFail([], '参数缺失');
        }
        if (!empty($model = TablesService::update($data))) {
            return $this->jsonSuccess($model, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }

    // 删除sku
    public function actionDelete ()
    {
        // 这里要判断是否为本店铺管理员。
        $food_id = Yii::$app->request->post('id');
        
        if (FoodSKUService::delete($food_id)) {
            return $this->jsonSuccess([], '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
}