<?php

namespace backend\modules\mall\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Lottery;
use backend\modules\mall\models\LotteryPrizes;

class PrizesController extends BaseController
{
    public function actionIndex()
    {
    	$lottery_id = Yii::$app->request->get('lottery_id');
    	$user_id = $this->getUserId ();
    	$lotterys = Lottery::find()->select(user_id)->where('lottery_id=:lottery_id',[':lottery_id'=>$lottery_id])->one();
    	if($lotterys['user_id']!=$user_id){
    		return $this->jsonFail ( [], '无法权限查看' );
    	}
    	$prizes_list = LotteryPrizes::find()->where('lottery_id=:lottery_id',[':lottery_id'=>$user_id])->asArray()->all();
    	return $this->jsonSuccess ($prizes_list);
    }
    public function actionView(){
    	$prize_id = Yii::$app->request->get('prize_id');
    	$prizes = Lottery::find()->select(user_id)->where('id=:id',[':id'=>$prize_id])->one();
    	$user_id = $this->getUserId ();
    	$lotterys = Lottery::find()->select(user_id)->where('id=:id',[':id'=>$prizes['lottery_id']])->one();
    	if($lotterys['user_id']!=$user_id){
    		return $this->jsonFail ( [], '无法权限查看' );
    	}
    	//$prizes = Lottery::find()->select(user_id)->where('id=:id',[':id'=>$prize_id])->one();
    	return $this->jsonSuccess ($prizes);
    }
	public function actionCreatelottery(){
		$user_id = $this->getUserId ();
		if(!empty(Yii::$app->request->get('title'))){
			return $this->jsonFail ( [ ], '参数不完整' );
		}
		$lottery = new Lottery();
		$lottery->user_id = $user_id;
		$lottery->lottery_title = Yii::$app->request->get('title');
		$lottery->creat_time = time();
		if(!empty(Yii::$app->request->get('content'))){
			$lottery->lottery_content= Yii::$app->request->get('content');
		}
		if(!empty(Yii::$app->request->get('image'))){
			$lottery->image= Yii::$app->request->get('image');
		}
		if(!empty(Yii::$app->request->get('mould_id'))){
			$lottery->mould_id = Yii::$app->request->get('mould_id');
		}
		if ($lottery->save ()) {
			$this->jsonSuccess ($lottery);
		}else{
			return $this->jsonFail ( [ ], '创建活动失败' );
		}
	}
}
