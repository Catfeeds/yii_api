<?php
namespace backend\modules\restaurant\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\CategoryService;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{

    // 获取店铺内所有分类
    public function actionIndex ()
    {
        $siteid = $this->getSite();
        if(empty($siteid)){
            return $this->jsonFail([],'参数不完整');
        }
        $category = CategoryService::showbysite($siteid);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            return $this->jsonSuccess($category, '查询成功');
        }
    }

    // 创建文件分类
    public function actionCreate ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if(!empty($model = CategoryService::create($data))){
            return $this->jsonSuccess($model,'创建成功');
        }else{
            return $this->jsonFail([],'创建失败');
        }
    }

    // 修改分类
    public function actionUpdate ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if(empty($data['cat_id'])){
            return $this->jsonFail([],'参数缺失');
        }
        if(!empty($model = CategoryService::update($data))){
            return $this->jsonSuccess($model,'修改成功');
        }else{
            return $this->jsonFail([],'修改失败');
        }
    }
    //分类置顶
    public function actionTotop(){
        
        // 这里要判断是否为本店铺管理员。
        $cat_id = Yii::$app->request->get('catid');
        if(empty($cat_id)){
            return $this->jsonFail([],'参数不完整');
        }
        if(CategoryService::totop($cat_id)){
            return $this->jsonSuccess([],'置顶成功');
        }else{
            return $this->jsonFail([],'置顶失败');
        }
    }
    // 删除店铺分类
    public function actionDelete ()
    {
        
        // 这里要判断是否为本店铺管理员。
        $cat_id = Yii::$app->request->get('catid');
        
        if(CategoryService::delete($cat_id)){
            return $this->jsonSuccess([],'删除成功');
        }else{
            return $this->jsonFail([],'删除失败');
        }
    }
}