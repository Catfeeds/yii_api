<?php

namespace backend\modules\ucenter\controllers;

use Yii;
use backend\modules\mall\models\User;
use backend\base\BaseController;
use yii\data\Pagination;
use backend\modules\mall\models\Address;

class AddressController extends BaseController {
	//显示用户的所有收货地址
	public function actionAlladdress(){
		$user_id = Yii::$app->request->get('user_id');
		if(empty($user_id)){
			return $this->jsonFail([],'用户id为空');
		}
		$address = Address::find()->where('user_id = :user_id',[':user_id'=>$user_id])->all();
		if(empty($address)){
			return $this->jsonFail([], '未查询到');
		}
		return $this->jsonSuccess($address,'地址列表');
	}
	//显示具体的地址
	public function actionView(){
		$address_id = Yii::$app->request->get('address_id');
		if(empty($address_id)){
			return $this->jsonFail([],'地址id为空');
		}
		$address = Address::find()->where('address_id = :address_id',[':address_id'=>$address_id])->one();
		if(empty($address)){
			return $this->jsonFail([],'未查询到');
		}
		return $this->jsonSuccess($address,'地址');
	}
}
