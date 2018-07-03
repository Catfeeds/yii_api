<?php
namespace backend\modules\cms\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\cms\models\Category;
use backend\modules\cms\service\ArticlecategoryService;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class ArticlecategoryController extends BaseController
{

    // 获取店铺内所有文件分类
    public function actionIndex ()
    {
        $site_id = $this->getSite();
        $category = ArticlecategoryService::showallcategory($site_id);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            return $this->jsonSuccess($category, '查询成功');
        }
    }
    // 获取店铺内所有文件分类
    public function actionShowall ()
    {
        $site_id = $this->getSite();
        $category = ArticlecategoryService::showcategory($site_id);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            return $this->jsonSuccess($category, '查询成功');
        }
    }
    // 根据pid获取分类
    public function actionIndexwithpid ()
    {
        $site_id = $this->getSite();
        $pid = Yii::$app->request->get('parentid');
        if(empty($pid)){
            $pid = 0;
        }
        $category = ArticlecategoryService::showallcategorywithpid($site_id,$pid);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            return $this->jsonSuccess($category, '查询成功');
        }
    }
    // 创建店铺分类
    public function actionCreate ()
    {
        $data = Yii::$app->request->post();
        if(!empty($model = ArticlecategoryService::create($data))){
            return $this->jsonSuccess($model, '创建成功');
        } else {
            return $this->jsonFail([], '创建失败');
        }
    }

    // 修改文章分类
    public function actionUpdate ()
    {
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if(!empty($model = ArticlecategoryService::update($data))){
            return $this->jsonSuccess($model, '创建成功');
        } else {
            return $this->jsonFail([], '创建失败');
        }
    }

    // 删除文章分类
    public function actionDelete ()
    {
        // 这里要判断是否为本店铺管理员。
        $site_id = $this->getSite();
        $id = Yii::$app->request->post('id');
        $child = ArticlecategoryService::showallcategorywithpid($site_id,$id);
        if(!empty($child)){
            return $this->jsonFail([],'不允许删除含有子分类的分类！');
        }
        if (ArticlecategoryService::delete($site_id, $id)) {
            return $this->jsonSuccess([], '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
    //面包屑导航
    public function actionShowallparent()
    {
        // 这里要判断是否为本店铺管理员。
        $site_id = $this->getSite();
        $id = Yii::$app->request->post('id');
        $model = ArticlecategoryService::showallparent($site_id, $id);
        if(empty($model)){
            return $this->jsonFail([],'未查到询');
        }
        return $this->jsonSuccess($model,'查询成功');
    }
}