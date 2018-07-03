<?php

namespace api\modules\v1\controllers;

use Yii;
use backend\modules\mall\models\Comment;
use yii\data\Pagination;
use api\base\BaseController;
use backend\modules\mall\models\OrderGoods;
use backend\modules\mall\models\Order;
use yii\db\Command;

/**
 * CommentController implements the CRUD actions for Comment model.
 */
class CommentController extends BaseController {
	
	/**
	 * Lists all Comment models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$query = new Comment ();
		$query = $query->find ();
		$pagination = new Pagination ( [ 
				'totalCount' => $query->count (),
				'defaultPageSize' => '20' 
		] );
		$list = $query->orderBy ( [ 
				'comment_id' => SORT_DESC 
		] )->offset ( $pagination->offset )->limit ( $pagination->limit )->all ();
		return $this->jsonSuccessWithPage ( $list, $pagination );
	}
	
	// 通过goods_id 查看评价
	// public function actionCommentwithgoods ()
	// {
	// $query = new Comment();
	// $query = $query->find();
	// $goods_id = $this->getParam('goods_id');
	// if(empty($goods_id)){
	// return $this->jsonFail([], '参数不全');
	// }
	// $pagination = new Pagination(
	// [
	// 'totalCount' => $query->where('goods_id=:goods_id',
	// [
	// ':goods_id' => $goods_id
	// ])->count(),
	// 'defaultPageSize' => '20'
	// ]);
	// $pagination->setPage(
	// empty($this->getParam('page')) ? '1' : $this->getParam('page'));
	// $list = $query->where('goods_id=:goods_id', [
	// ':goods_id' => $goods_id
	// ])
	// ->orderBy([
	// 'comment_id' => SORT_DESC
	// ])
	// ->offset($pagination->offset)
	// ->limit($pagination->limit)
	// ->all();
	// if(empty($list)){
	// return $this->jsonFail([], '未查找到');
	// }
	// return $this->jsonSuccessWithPage($list, $pagination);
	// }
	public function actionCommentwithgoods() {
		$goods_id = Yii::$app->request->post ( 'goods_id' );
		if (empty ( $goods_id )) {
			return $this->jsonFail ( [ ], '请输入商品号' );
		}
		$page_info = new Pagination ( [ 
				'totalCount' => Comment::findgoods ( $goods_id ),
				'defaultPageSize' => empty ( $this->getParam ( 'per-page' ) ) ? '10' : $this->getParam ( 'per-page' ) 
		] );
		$page_info->setPage ( $this->getParam ( 'page' ) - 1 );
		$comment = Comment::findByGoods ( $goods_id, $page_info->offset, $page_info->limit );
		if (empty ( $comment )) {
			return $this->jsonFail ( [ ], '未查询到' );
		} else {
			return $this->jsonSuccess ( $comment, '查询成功' );
		}
	}
	
	
	// 查看自己的评价列表
	public function actionMycommentlist() {
		$user_id = $this->getUserId ();
		// 测试固定user_id = 1
		// $user_id = 1;
		$query = new Comment ();
		$query = $query->find ();
		
		$pagination = new Pagination ( [ 
				'totalCount' => $query->where ( 'user_id=:user_id', [ 
						':user_id' => $user_id 
				] )->count (),
				'defaultPageSize' => '20' 
		] );
		$pagination->setPage ( empty ( $this->getParam ( 'page' ) ) ? '1' : $this->getParam ( 'page' ) );
		$list = $query->where ( 'user_id=:user_id', [ 
				':user_id' => $user_id 
		] )->orderBy ( [ 
				'comment_id' => SORT_DESC 
		] )->offset ( $pagination->offset )->limit ( $pagination->limit )->all ();
		return $this->jsonSuccessWithPage ( $list, $pagination );
	}
	
	// 通过commentid查询具体评论
	public function actionView() {
		$query = new Comment ();
		$query = $query->find ();
		$comment_id = $this->getParam ( 'comment_id' );
		
		$comment = $query->where ( 'comment_id=:comment_id', [ 
				':comment_id' => $comment_id 
		] )->one ();
		return $this->jsonSuccess ( [ 
				'comment' => $comment 
		] );
	}
	
	// 添加评论
	public function actionCreate() {
		$user_id = $this->getUserId ();
		$data = Yii::$app->request->post ();
		$goods_id = Yii::$app->request->post ( 'goods_id' );
		$order_goods = OrderGoods::find ()->where ( 'goods_id =:goods_id and is_comment=:is_comment', [ 
				'goods_id' => $goods_id,
				':is_comment' => Comment::NOT_COMMENT 
		] )->one ();
		if ($order_goods == null) {
			return $this->jsonFail ( [ ], '没有购买/已评价' );
		}
		$order = Order::find ()->where ( 'order_id=:order_id and user_id=:user_id', [ 
				':order_id' => $order_goods ['order_id'],
				':user_id' => $user_id 
		] )->one ();
		if (empty ( $order )) {
			return $this->jsonFail ( [ ], '您不能评价此商品' );
		}
		$comment = new Comment ();
		if ($comment->load ( $data, '' ) && $comment->validate ()) {
			$comment->add_time = time ();
			$order_goods->is_comment = COMMENT::IS_COMMENT;
			if (! ($comment->save ()) || ! ($order_goods->save ())) {
				return $this->jsonFail ( [ ], '添加失败' );
			} else {
				return $this->jsonSuccess ( [ 
						$comment 
				], '评价成功' );
			}
		} else {
			return $this->jsonFail ( [ ], '请填写评价' );
		}
	}
	public function actionUpdate() {
		$user_id = $this->getUserId ();
		$comment_id = $this->getParam ( 'comment_id' );
		$comment = Comment::find ()->where ( 'user_id=:user_id and comment_id = :comment_id', [ 
				':user_id' => $user_id,
				':comment_id' => $comment_id 
		] );
		if (empty ( $comment )) {
			return $this->jsonFail ( [ ], '无法修改此评价' );
		}
		$data = Yii::$app->request->post ();
		$model = new Comment ();
		if ($model->load ( $data, '' ) && $model->validate ()) {
			$comment->add_time = time ();
			if (! ($comment->update ())) {
				return $this->jsonFail ( [ ], '添加失败' );
			} else {
				return $this->jsonSuccess ( [ 
						$comment 
				], '评价成功' );
			}
		} else {
			return $this->jsonFail ( [ ], '请填写评价' );
		}
	}
}
