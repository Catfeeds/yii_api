<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use yii\data\Pagination;
use backend\modules\mall\service\Goods_category;
use backend\modules\mall\models\Category;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{

    public $modelClass = 'backend\modules\content\models\Category';

    public $serializer = [
            'class' => 'yii\rest\Serializer',
            'collectionEnvelope' => 'items'
    ];

    // 获取所有分类
    public function actionIndex ()
    {
        $page_info = new Pagination(
                [
                        'totalCount' => Goods_category::find()->where(
                                'is_show=:is_show', 
                                [
                                        ':is_show' => 1
                                ])->count(),
                        'defaultPageSize' => empty(
                                Yii::$app->request->get('per-page')) ? '20' : Yii::$app->request->get(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $categorys = Goods_category::find()->where('is_show=:is_show', 
                [
                        ':is_show' => 1
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($categorys, $page_info);
    }

    public function actionGetchildidindex ()
    {
        
        $catid = Yii::$app->request->get('catid');
        $category_ids = Goods_category::getallchildcatid($catid);
        return $this->jsonSuccess($category_ids);
    }

    // 根据pid取得父节点。
    public function actionGetfatherindex ()
    {
        
        $catid = Yii::$app->request->get('catid');
        $data = Goods_category::get_father($catid);
        return $this->jsonSuccess($data);
    }

    // 根据pid取得所有父节点集合。
    public function actionGetallfatherindex ()
    {
        
        $catid = Yii::$app->request->get('catid');
        $data = array();
        $category = Goods_category::get_father($catid);
        array_push($data, $category);
        while ($category['level'] != 0) {
            $category = Goods_category::get_father($category['parentid']);
            array_push($data, $category);
        }
        return $this->jsonSuccess($data);
    }

    // 根据pid取得分类
    public function actionGetindexbypid ()
    {
        
        $pid = Yii::$app->request->get('pid');
        if ($pid == null){
            $pid = 0;
        }
        $page_info = new Pagination(
                [
                        'totalCount' => Goods_category::find()->where(
                                'parentid=:parentid and is_show=:is_show', 
                                [
                                        'parentid' => $pid,
                                        ':is_show' => 1
                                ])->count(),
                        'defaultPageSize' => empty(
                                Yii::$app->request->get('per-page')) ? '20' : Yii::$app->request->get(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $categorys = Goods_category::find()->where(
                'parentid=:parentid and is_show=:is_show', 
                [
                        'parentid' => $pid,
                        ':is_show' => 1
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($categorys, $page_info);
    }

    // 根据目录等级获取分类
    public function actionGetlevelindex ()
    {
        
        $level = Yii::$app->request->get('level');
        if ($level == null)
            $level = 0;
        
        $page_info = new Pagination(
                [
                        'totalCount' => Goods_category::find()->where(
                                'level=:level and is_show=:is_show', 
                                [
                                        'level' => $level,
                                        ':is_show' => 1
                                ])->count(),
                        'defaultPageSize' => empty(
                                Yii::$app->request->get('per-page')) ? '20' : Yii::$app->request->get(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $categorys = Goods_category::find()->where(
                'level=:level and is_show=:is_show', 
                [
                        'level' => $level,
                        ':is_show' => 1
                ])
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($categorys, $page_info);
    }

    // 获取level1父节点到本节点的名称id
    public function actionGetallfathertotis ()
    {
        
        $catid = Yii::$app->request->get('catid');
        $data = array();
        $categorys = Goods_category::find()->where(
                [
                        'catid=:catid',
                        'catid' => $catid
                ])->one();
        
        array_push($data, $categorys);
        while ($categorys['level'] != 1) {
            $categorys = Goods_category::getfathercatid($catid);
            array_push($data, $categorys);
        }
        
        return $data;
    }

    // 根据id获取单个
    public function actionView ()
    {
        
        $id = Yii::$app->request->get('id');
        $data = Goods_category::get_my_child_api($id);
        return $this->jsonSuccess($data);
    }

    // 创建新分类接口
    public function actionCreate ()
    {
        $model = new Category();
        $model->name = Yii::$app->request->get('name');
        $model->image = Yii::$app->request->get('image');
        if (Yii::$app->request->get('parentid') != null) {
            $category = Goods_category::get_father(
                    Yii::$app->request->get('parentid'));
            $model->parentid = Yii::$app->request->get('parentid');
            $model->level = $category['level'] + 1;
        } else {
            $model->level = 1;
            $model->parent_id_path = "0";
        }
        
        if (Yii::$app->request->get('mobile_name') == null) {
            $model->mobile_name = $model->name;
        }
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        $model->parent_id_path = Goods_category::get_father($model->parentid)['parent_id_path'] .
                 '_' . $model->catid;
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $this->jsonSuccess($model);
    }

    // 修改分类接口
    public function actionUpdate ()
    {
        
        $id = Yii::$app->request->get('id');
        $model = Goods_category::get_my($id);
        if (Yii::$app->request->getm('name') != null)
            $model->name = $this->getParam('name');
        if (Yii::$app->request->get('image') != null)
            $model->image = $this->getParam('image');
        if (Yii::$app->request->get('mobile_name') != null)
            $model->mobile_name = $this->getParam('mobile_name');
        if (Yii::$app->request->get('parentid') != null)
            $model->parentid = $this->getParam('parentid');
        if (($category = Goods_category::get_father($model->parentid)) != null) {
            
            $model->parentid = $this->getParam('parentid');
            $model->level = $category['level'] + 1;
            $model->parent_id_path = $category['parent_id_path'] . '_' .
                     $model->catid;
        }
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $this->jsonSuccess($model);
    }

    // 删除接口
    public function actionDelete ()
    {
        
        $id = $this->getParam('id');
        $model = Goods_category::get_my($id);
        if (! $model->delete()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $this->jsonSuccess($model);
    }
}