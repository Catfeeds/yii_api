<?php

namespace backend\modules\mall\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Lottery;

class LotteryController extends BaseController {
	public function actionIndex() {
		$user_id = $this->getUserId ();
		$lottery_list = Lottery::find ()->where ( 'user_id=:user_id', [ 
				':user_id' => $user_id 
		] )->asArray ()->all ();
		return $this->jsonSuccess ( $lottery_list );
	}
	public function actionView() {
		$user_id = $this->getUserId ();
		$lottery_id = Yii::$app->request->get ( 'lottery_id' );
		$lottery = Lottery::find ()->where ( 'user_id=:user_id and lottery_id = :lottery_id', [ 
				':user_id' => $user_id,
				':lottery_id' => $lottery_id 
		] )->asArray ()->all ();
		return $this->jsonSuccess ( $lottery );
	}
	public function actionCreatelottery() {
		$user_id = $this->getUserId ();
		if (! empty ( Yii::$app->request->get ( 'title' ) )) {
			return $this->jsonFail ( [ ], '参数不完整' );
		}
		$lottery = new Lottery ();
		$lottery->user_id = $user_id;
		$lottery->lottery_title = Yii::$app->request->get ( 'title' );
		$lottery->creat_time = time ();
		if (! empty ( Yii::$app->request->get ( 'content' ) )) {
			$lottery->lottery_content = Yii::$app->request->get ( 'content' );
		}
		if (! empty ( Yii::$app->request->get ( 'image' ) )) {
			$lottery->image = Yii::$app->request->get ( 'image' );
		}
		if (! empty ( Yii::$app->request->get ( 'mould_id' ) )) {
			$lottery->mould_id = Yii::$app->request->get ( 'mould_id' );
		}
		if ($lottery->save ()) {
			$this->jsonSuccess ( $lottery );
		} else {
			return $this->jsonFail ( [ ], '创建活动失败' );
		}
	}
	
}
