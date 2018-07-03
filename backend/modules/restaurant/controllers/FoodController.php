<?php
namespace backend\modules\restaurant\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\FoodService;
use backend\modules\restaurant\service\FoodSKUService;
use backend\modules\restaurant\service\PropertyService;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class FoodController extends BaseController
{

    // 获取所有菜品
    public function actionIndex ()
    {
        $siteid = $this->getSite();
        if (empty($siteid)) {
            return $this->jsonFail([], '参数缺失');
        }
        $foods = FoodService::showbysite($siteid);
        if (empty($foods)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($foods, '查询成功');
        }
    }

    // 按分类查询
    public function actionIndexbycatid ()
    {
        $site_id = $this->getSite();
        $cat_id = Yii::$app->request->get('cat_id');
        if (empty($site_id) || empty($cat_id)) {
            return $this->jsonFail([], '参数缺失');
        }
        $foods = FoodService::showbycatid($site_id, $cat_id);
        if (empty($foods)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($foods, '查询成功');
        }
    }

    // 获取所有菜品 是否在售
    public function actionIndexwithsale ()
    {
        $siteid = $this->getSite();
        $sale = Yii::$app->request->get('is_on_sale');
        if (empty($siteid)) {
            return $this->jsonFail([], '参数缺失');
        }
        $foods = FoodService::showbycatidwithsale($siteid,$sale);
        if (empty($foods)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($foods, '查询成功');
        }
    }

    // 按分类查询在售菜品 是否在售
    public function actionIndexbycatidwithsale ()
    {
        $site_id = $this->getSite();
        $cat_id = Yii::$app->request->get('cat_id');
        $sale = Yii::$app->request->get('is_on_sale');
        if (empty($site_id) || empty($cat_id)) {
            return $this->jsonFail([], '参数缺失');
        }
        $foods = FoodService::showsitewithsale($site_id, $cat_id,$sale);
        if (empty($foods)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($foods, '查询成功');
        }
    }
    //查询具体信息
    public function actionView()
    {
        $site_id = $this->getSite();
        $food_id = Yii::$app->request->get('food_id');
        if (empty($food_id)) {
            return $this->jsonFail([], '参数缺失');
        }
        $food = FoodService::showthis($food_id);
        if(!empty($food)){
            return $this->jsonSuccess($food,'查询完成');
        }else{
            return $this->jsonFail([],'未查询到');
        }
    }
    //带有sku和属性的创建
    public function actionCreatefood()
    {
        
        $site_id = $this->getSite();
        
        $food = Yii::$app->request->post('food');
        $skus = Yii::$app->request->post('sku');
        $pros = Yii::$app->request->post('pro');
        $model = FoodService::create($food,$site_id);
        if(empty($model)){
            return $this->jsonFail([],'创建失败');
        }
        if(!empty($skus)){
            foreach ($skus as $sku)
            {
                $sku['food_id'] = $model->food_id;
                FoodSKUService::create($sku);
            }
        }
        if(!empty($pros)){
            foreach ($pros as $pro)
            {
                $my_pro = $pro['property'];
                $my_pro['food_id']= $model->food_id;
                $my_pro_child = $pro['property_child'];
                PropertyService::createwithchild($my_pro, $my_pro_child);
            }
        }
        
        return $this->jsonSuccess($model);
    }
    // 修改菜品
    public function actionUpdate ()
    {
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post('food');
        $skus = Yii::$app->request->post('sku');
        $pros = Yii::$app->request->post('pro');
        if (empty($data['food_id'])) {
            return $this->jsonFail([], '参数缺失');
        }
        $model = FoodService::update($data);
        if(empty($model)){
            return $this->jsonFail([],'修改失败');
        }
        if(!empty($skus)){
            foreach ($skus as $sku)
            {
                $sku['food_id'] = $model->food_id;
                if(empty($sku['id'])){
                    FoodSKUService::create($sku);
                }else{
                    FoodSKUService::update($sku);
                }
            }
        }
        if(!empty($pros)){
            foreach ($pros as $pro)
            {
                $my_pro = $pro['property'];
                $my_pro['food_id']= $model->food_id;
                $my_pro_child = $pro['property_child'];
                if(empty($my_pro['property_id'])){
                    PropertyService::createwithchild($my_pro, $my_pro_child);
                }else{
                    PropertyService::updatewithchilde($my_pro, $my_pro_child);
                }
            }
        }
        if (!empty($model->save())) {
            return $this->jsonSuccess($model, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }

    // 删除菜品
    public function actionDelete ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $food_id = Yii::$app->request->post('food_id');
        if (empty($food_id)) {
            return $this->jsonFail([], '参数缺失');
        }
        if (FoodService::delete($food_id)) {
            return $this->jsonSuccess([], '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
    //菜品上架 下架
    public function actionSale()
    {
        
        // 这里要判断是否为本店铺管理员。
        $food_id = Yii::$app->request->post('food_id');
        $sale = Yii::$app->request->post('sale');
        if (empty($food_id)) {
            return $this->jsonFail([], '参数缺失');
        }
        if (FoodService::saleornot($food_id,$sale)) {
            return $this->jsonSuccess([], '修改成功');
        } else {
            return $this->jsonFail([],'修改失败');
        }
    }
}