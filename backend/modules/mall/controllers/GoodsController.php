<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use yii\data\Pagination;
use backend\modules\mall\service\Goods_category;
use backend\modules\mall\service\Goods_service;
use backend\modules\mall\models\Goods;
use backend\modules\mall\models\SpecGoodsPrice;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class GoodsController extends BaseController
{

    public $modelClass = 'backend\modules\content\models\Goods';

    public $serializer = [
            'class' => 'yii\rest\Serializer',
            'collectionEnvelope' => 'items'
    ];

    public static function thewhere ($sale, $in_stock)
    {
        $where = '';
        if ($sale != null) {
            $where .= 'and is_on_sale = ' . $sale . ' ';
        }
        if ($in_stock == '0') {
            $where .= 'and store_count = 0';
        }
        if (empty($where)) {
            return '';
        } else {
            return substr($where, 3);
        }
    }

    // 获取所有商品
    public function actionIndex ()
    {
        
        $sale = Yii::$app->request->get('sale');
        $in_stock = Yii::$app->request->get('in_stock');
        $where = $this->thewhere($sale, $in_stock);
        
        $page_info = new Pagination(
                [
                        'totalCount' => Goods::find()->where($where)->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $goods = Goods::find()->where($where)
            ->orderBy('goods_id desc')
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($goods, $page_info);
    }

    // 通过商品分类获得(不获得子分类的商品)
    public function actionGetindexbycategoryid ()
    {
        
        $cat_id = $this->getParam('catid');
        $where = $this->thewhere();
        $page_info = new Pagination(
                [
                        'totalCount' => Goods::find()->where('cat_id=:cat_id', 
                                [
                                        'cat_id' => $cat_id
                                ])
                            ->where($where)
                            ->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $Goods = Goods::find()->where('cat_id=:cat_id', 
                [
                        'cat_id' => $cat_id
                ])
            ->where($where)
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($Goods, $page_info);
    }

    // 通过商品分类获得 包括子类商品
    public function actionGetallindexbycategoryid ()
    {
        
        $cat_id = $this->getParam('catid');
        $child_catids = Goods_category::getallchildcatid($cat_id);
        $where = $this->thewhere();
        
        $page_info = new Pagination(
                [
                        'totalCount' => Goods::find()->where(
                                [
                                        'in',
                                        'cat_id',
                                        $child_catids
                                ])
                            ->where($where)
                            ->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $Goods = Goods::find()->select('cat_id,Goods_sn,Goods_name')
            ->where(
                [
                        'in',
                        'cat_id',
                        $child_catids
                ])
            ->where($where)
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($Goods, $page_info);
    }

    // 分类查询 并加上价格区间
    public function actionGetindexbycategoryidandprice ()
    {
        
        $pricelow = $this->getParam('pricelow');
        $pricehigh = $this->getParam('pricehigh');
        if ($pricelow == null || $pricehigh == null || $pricehigh < $pricelow) {
            return $this->jsonFail([], '价格错误');
        }
        $cat_id = $this->getParam('catid');
        $child_catids = Goods_category::getallchildcatid($cat_id);
        $where = $this->thewhere();
        $page_info = new Pagination(
                [
                        'totalCount' => Goods::find()->where(
                                [
                                        'in',
                                        'cat_id',
                                        $child_catids
                                ])
                            ->andWhere(
                                [
                                        'between',
                                        'shop_price',
                                        $pricelow,
                                        $pricehigh
                                ])
                            ->where($where)
                            ->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $Goods = Goods::find()->select('cat_id,Goods_sn,Goods_name')
            ->where(
                [
                        'in',
                        'cat_id',
                        $child_catids
                ])
            ->andWhere(
                [
                        'between',
                        'shop_price',
                        $pricelow,
                        $pricehigh
                ])
            ->where($where)
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($Goods, $page_info);
    }

    // 通过商品分类获得 包括子类商品
    public function actionGetallGoods ()
    {
        
        $cat_id = $this->getParam('catid');
        $child_catids = Goods_category::getallchildcatid($cat_id);
        $where = $this->thewhere();
        $page_info = new Pagination(
                [
                        'totalCount' => Goods::find()->where(
                                [
                                        'in',
                                        'cat_id',
                                        $child_catids
                                ])
                            ->where($where)
                            ->andwhere($where)
                            ->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $Goods = Goods::find()->select('cat_id,Goods_sn,Goods_name')
            ->where(
                [
                        'in',
                        'cat_id',
                        $child_catids
                ])
            ->where($where)
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($Goods, $page_info);
    }

    // 根据店铺内的分类id获取商品
    public function actionIndexbysitecatid ()
    {
        
        $cat_id = Yii::$app->request->post('catid');
        $where = $this->thewhere();
        $page_info = new Pagination(
                [
                        'totalCount' => Goods::find()->where(
                                'extend_cat_id=:extend_cat_id', 
                                [
                                        '_extendcat_id' => $cat_id
                                ])
                            ->where($where)
                            ->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage($this->getParam('page')-1);
        $Goods = Goods::find()->where('extend_cat_id=:extend_cat_id', 
                [
                        'extend_cat_id' => $cat_id
                ])
            ->where($where)
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($Goods, $page_info);
    }

    // 根据id获取单个
    public function actionView ()
    {
        
        $id = Yii::$app->request->get('id');
        $data = Goods_service::get_child_one_api($id);
        $specgoods = SpecGoodsPrice::getbygoodsid($data['goods_id']);
        return $this->jsonSuccess(
                [
                        'good' => $data,
                        'specgoods' => $specgoods
                ], '查询成功');
    }

    // 创建商品
    public function actionCreate ()
    {
        
        $model = new Goods();
        if (empty(Yii::$app->request->post('shipping_area_ids'))) {
            $model->shipping_area_ids = '0';
        }
        if (empty(Yii::$app->request->post('weight'))) {
            $model->weight = 1;
        }
        if (empty(Yii::$app->request->post('SKU'))) {
            $SKU = Goods::NO_SKU;
        } else {
            $model->store_count = 0;
            $model->shop_price = 0;
        }
        $model->last_update = time();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            if (! $model->save()) {
                return array_values($model->getFirstErrors())[0];
            }
            return $this->jsonSuccess($model, '添加成功');
        } else {
            return $this->jsonFail([], '参数不完整');
        }
    }

    public function actionUpdate ()
    {
        
        $model = Goods_service::get_child_one_api(
                Yii::$app->request->post('goods_id'));
        if (empty($model)) {
            return $this->jsonFail([], '未查询到此商品');
        }
        $model->last_update = time();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            if ($model->save()) {
                return $this->jsonSuccess($model, '修改成功');
            }
        }
        return $this->jsonFail($model, '修改失败');
    }

    public function actionDelete ()
    {
        
        $id = Yii::$app->request->post('goods_id');
        $model = Goods_service::get_child_one_api($id);
        if (empty($model)) {
            return $this->jsonFail([
                    $id
            ], '未查询到');
        }
        if (! $model->delete()) {
            return $this->jsonFail($model, '删除失败');
        }
        return $this->jsonSuccess([], '删除成功');
    }
}