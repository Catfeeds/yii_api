<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Category;
use backend\modules\mall\models\SiteCategory;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class SitecategoryController extends BaseController
{

    // 获取店铺内所有分类
    public function actionIndex ()
    {
        
        $site_id = Yii::$app->request->get('site_id');
        $category = SiteCategory::showmycategory($site_id);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            return $this->jsonSuccess($category, '查询成功');
        }
    }

    // 根据pid获取分组
    public function actionIndexbypid ()
    {
        
        $site_id = Yii::$app->request->get('site_id');
        $pid = Yii::$app->request->get('parentid');
        if (empty($site_id) || empty($pid)) {
            return $this->jsonFail('参数不完整');
        }
        $category = SiteCategory::showmycategorybypid($site_id, $pid);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            return $this->jsonSuccess($category, '查询成功');
        }
    }

    // 创建新分类接口
    public function actionCreate ()
    {
       
        $data = Yii::$app->request->post();
        $model = new SiteCategory();
        $model->load($data, '');
        if ($model->validate()) {
            if (! $model->save()) {
                return $this->jsonFail($model, '创建失败');
                return array_values($model->getFirstErrors())[0];
            } else {
                return $this->jsonSuccess($model, '创建成功');
            }
        } else {
            return $this->jsonFail([], '参数不完整');
        }
    }

    // 修改分类接口
    public function actionUpdate ()
    {
       
        $data = Yii::$app->request->post();
        $model = SiteCategory::findbyid($data['id']);
        if (empty($model)) {
            return $this->jsonFail([], '未查询到此分类');
        }
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            if ($model->save()) {
                return $this->jsonSuccess($model, '修改成功');
            }
        }
        return $this->jsonFail($model, '修改失败');
    }

    // 删除接口
    public function actionDelete ()
    {
        
        $id = Yii::$app->request->post('id');
        ;
        $model = SiteCategory::findbyid($id);
        if (! $model->delete()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $this->jsonSuccess($model, '删除成功');
    }

    // 生成树形结构制表符
    public function actionTreeall ()
    {
        
        $site_id = Yii::$app->request->post('site_id');
        $category = SiteCategory::showmycategoryasarray($site_id);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            $tree = $this->_generateTreenew($category);
            return $this->jsonSuccess($tree, '树形结构');
        }
    }

    // 树形结构
    public function actionTree ()
    {
        
        $site_id = Yii::$app->request->post('site_id');
        $category = SiteCategory::showmycategoryasarray($site_id);
        if (empty($category)) {
            return $this->jsonFail([], '未查询到分类');
        } else {
            $tree = $this->_generateTree($category);
            return $this->jsonSuccess($tree, '树形结构');
        }
    }

    // 生成树形结构
    private static function _generateTree ($data, $pid = 0)
    {
        $tree = [];
        if ($data && is_array($data)) {
            foreach ($data as $v) {
                if ($v['parentid'] == $pid) {
                    $tree[] = [
                            'id' => $v['id'],
                            'category_name' => $v['category_name'],
                            'parentid' => $v['parentid'],
                            'children' => self::_generateTree($data, $v['id'])
                    ];
                }
            }
        }
        return $tree;
    }

    // 生成树形图
    private static function _generateTreenew ($data, $pid = 0)
    {
        $tree = [];
        $header = ' ├ ';
        if ($pid == 0) {
            $header = ' ├ ';
        } else {
            $header = ' | ' . $header;
        }
        if ($data && is_array($data)) {
            
            foreach ($data as $v) {
                if ($v['parentid'] == $pid) {
                    $tree[] = [
                            'id' => $v['id'],
                            'category_name' => $header . '' . $v['category_name'],
                            'parentid' => $v['parentid']
                    ];
                    $tree = array_merge_recursive($tree, 
                            self::_generateTreenew($data, $v['id']));
                }
            }
        }
        return $tree;
    }
}