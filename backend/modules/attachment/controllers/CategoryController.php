<?php
namespace backend\modules\attachment\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\attachment\models\AttachmentCategory;
use backend\modules\attachment\models\Attachment;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{

    // 获取店铺内所有文件分类
    public function actionIndex ()
    {
        $site_id = Yii::$app->request->get('site_id');
        $category = AttachmentCategory::getAllCategory($site_id);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            return $this->jsonSuccess($category, '查询成功');
        }
    }

    // 创建店铺文件分类
    public function actionCreate ()
    {
        $site_id = Yii::$app->request->post('site_id');
        $name = Yii::$app->request->post('name');
        $category = new AttachmentCategory();
        $category->catid = AttachmentCategory::getTheCatid($site_id);
        $category->site_id = $site_id;
        $category->name = $name;
        $category->last_update = time();
        if ($category->validate() && $category->save()) {
            return $this->jsonSuccess($category, '创建成功');
        } else {
            return $this->jsonFail([], '创建失败');
        }
    }

    // 修改店铺文件分类名称
    public function actionUpdate ()
    {
        // 这里要判断是否为本店铺管理员。
        $site_id = Yii::$app->request->post('site_id');
        $catid = Yii::$app->request->post('catid');
        
        if (empty($site_id) || empty($catid)) {
            return $this->jsonFail([], '参数不完整');
        }
        if ($catid == AttachmentCategory::DEFAULT_ID) {
            return $this->jsonFail([], '默认分类不允许更改名称');
        }
        $category = AttachmentCategory::getCategory($site_id, $catid);
        if (empty($category)) {
            return $this->jsonFail([], '未找到此分类');
        }
        $name = Yii::$app->request->post('name');
        $category->name = $name;
        $category->last_update = time();
        if ($category->validate() && $category->save()) {
            return $this->jsonSuccess($category, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }

    // 删除店铺分类
    public function actionDelete ()
    {
        // 这里要判断是否为本店铺管理员。
        $site_id = Yii::$app->request->get('site_id');
        $catid = Yii::$app->request->get('catid');
        if (empty($site_id) || empty($catid)) {
            return $this->jsonFail([], '参数不完整');
        }
        if ($catid == AttachmentCategory::DEFAULT_ID) {
            return $this->jsonFail([], '默认分类不允许更改名称');
        }
        $category = AttachmentCategory::getCategory($site_id, $catid);
        if (empty($category)) {
            return $this->jsonFail([], '未找到此分类');
        }
        Attachment::updateAll([
                'catid' => AttachmentCategory::DEFAULT_ID
        ], [
                'catid' => $catid,
                'site_id' => $site_id
        ]);
        if ($category->delete()) {
            return $this->jsonSuccess($category, '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
}