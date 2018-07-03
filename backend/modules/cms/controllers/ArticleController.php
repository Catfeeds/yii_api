<?php

namespace backend\modules\cms\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\cms\models\Article;
use yii\data\Pagination;
use backend\modules\cms\service\ArticleService;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends BaseController {
	
	// 显示店铺内所有文章列表
	public function actionIndex() {
		$site_id = $this->getSite ();
		if (empty ( $site_id )) {
			return $this->jsonFail ( [ ], '未输入站点id' );
		}
		$page_info = new Pagination ( [ 
				'totalCount' => ArticleService::showbysitecount ( $site_id ),
				'defaultPageSize' => empty ( $this->getParam ( 'per-page' ) ) ? '20' : $this->getParam ( 'per-page' ) 
		] );
		$page_info->setPage ( $this->getParam ( 'page' ) - 1 );
		$articles = ArticleService::showbysite ( $site_id, $page_info->offset, $page_info->limit );
		return $this->jsonSuccessWithPage ( $articles, $page_info );
	}
	
	// 按分类查看内容
	public function actionIndexbycat() {
		$site_id = $this->getSite ();
		$cat_id = Yii::$app->request->get ( 'catid' );
		if (empty ( $site_id ) || empty ( $cat_id )) {
			return $this->jsonFail ( [ ], '参数不完整' );
		}
		$page_info = new Pagination ( [ 
				'totalCount' => ArticleService::showbycatcount ( $site_id, $cat_id ),
				'defaultPageSize' => empty ( $this->getParam ( 'per-page' ) ) ? '20' : $this->getParam ( 'per-page' ) 
		] );
		$page_info->setPage ( $this->getParam ( 'page' ) - 1 );
		$articles = ArticleService::showbycat ( $site_id, $cat_id, $page_info->offset, $page_info->limit );
		return $this->jsonSuccessWithPage ( $articles, $page_info );
	}
	
	// 查看文章内容
	public function actionView() {
		$articleid = Yii::$app->request->get ( 'articleid' );
		if (empty ( $articleid )) {
			return $this->jsonFail ( [ ], '参数不完整' );
		}
		$article = ArticleService::showarticle ( $articleid );
		if (empty ( $article )) {
			return $this->jsonFail ( [ ], '未查询到' );
		}
		return $this->jsonSuccess ( $article, '查询成功' );
	}
	
	// 新建文章
	public function actionCreate() {
		$user_id = $this->getUserId ();
		$site_id = $this->getSite ();
		$data = Yii::$app->request->post ();
		
		if (! empty ( $model = ArticleService::addarticle ( $data, $user_id, $site_id ) )) {
			return $this->jsonSuccess ( $model, '创建成功' );
		} else {
			return $this->jsonFail ( $model, '创建失败' );
		}
	}
	// 修改文章
	public function actionUpdate() {
		$user_id = $this->getUserId ();
		$site_id = $this->getSite ();
		$data = Yii::$app->request->post ();
		if (! empty ( $model = ArticleService::updatearticle ( $data, $user_id, $site_id ) )) {
			return $this->jsonSuccess ( $model, '创建成功' );
		} else {
			return $this->jsonFail ( $model, '创建失败' );
		}
	}
	// 删除文章
	public function actionDelete() {
		$user_id = $this->getUserId ();
		$id = Yii::$app->request->post ( 'id' );
		
		if (ArticleService::deletearticle ( $id )) {
			return $this->jsonSuccess ( [ ], '删除成功' );
		} else {
			return $this->jsonFail ( [ ], '删除失败' );
		}
	}
}