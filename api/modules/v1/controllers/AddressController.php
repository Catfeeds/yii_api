 <?php

/**用户注册流程
 */
namespace api\modules\v1\controllers;

use Yii;
use api\base\BaseController;
use backend\modules\mall\models\Address;
use yii\data\Pagination;

class AddressController extends BaseController {
	
	// 查询本人的所有收货地址
	public function actionShowmyaddress() {
		$user_id = $this->getUserId ();
		$address = Address::findByUser ( $user_id);
		if (empty ( $address )) {
			return $this->jsonFail ( [ ], '未查询到' );
		}
		return $this->jsonSuccess ( $address, '查询成功' );
	}
	
	// 查询本人的一个具体收货地址
	public function actionShowaddress() {
		$address_id = Yii::$app->request->get ( 'address_id' );
		$user_id = $this->getUserId ();
		$address = Address::findTheAddress ( $user_id, $address_id );
		if (empty ( $address )) {
			return $this->jsonFail ( [ ], '未查询到' );
		}
		return $this->jsonSuccess ( $address, '查询成功' );
	}
	
	// 创建收货地址
	public function actionCreate() {
		$user_id = $this->getUserId ();
		$data = Yii::$app->request->post ();
		$model = new Address ();
		if (! empty ( $data ['is_default'] )) {
			if ($data ['is_default'] == Address::STATUS_IS_DEFAULT) {
				$default_address = Address::findDefault ( $user_id );
				foreach ( $default_address as $key => $val ) {
					$val->is_default = Address::STATUS_NOT_DEFAULT;
					$val->update ();
				}
			}
		}
		if ($model->load ( $data, '' ) && $model->validate ()) {
			if ($model->save ()) {
				return $this->jsonSuccess ( $model, '添加成功' );
			} else {
				return $this->jsonFail ( [ ], '添加失败' );
			}
		}
	}
	// 修改默认收货地址
	public function actionUpdate() {
		$address_id = Yii::$app->request->post ( 'address_id' );
		if (empty ( $address_id )) {
			return $this->jsonFail ( [ ], '参数不全' );
		}
		$user_id = $this->getUserId ();
		$data = Yii::$app->request->post ();
		$model = new Address ();
		$model = Address::findTheAddress ( $user_id, $address_id );
		/*
		 * if($model->is_default==Address::STATUS_IS_DEFAULT&&Yii::$app->request->post('is_default')==0){
		 * return $this->jsonFail([],'您不能把默认地址修改为非默认地址');
		 * }
		 */
		if (empty ( $model )) {
			return $this->jsonFail ( [ ], '未查询到，请确认是您的收货地址' );
		}
		if (! empty ( $data ['is_default'] )) {
			if ($data ['is_default'] == 1) {
				$default_address = Address::findDefault ( $user_id );
				foreach ( $default_address as $key => $val ) {
					$val->is_default = Address::STATUS_NOT_DEFAULT;
					$val->update ();
				}
			}
		}
		if ($model->load ( $data, '' ) && $model->save ()) {
			return $this->jsonSuccess ( $model, '修改成功' );
		} else {
			return $this->jsonFail ( [ ], '修改失败' );
		}
	}
	
	// 删除地址
	public function actionDelete() {
		$user_id = Yii::$app->request->post ( 'user_id' );
		$address_id = Yii::$app->request->post ( 'address_id' );
		$model = Address::findTheAddress ( $user_id, $address_id );
		if (empty ( $model )) {
			return $this->jsonFail ( [ ], '未查询到，请确认是您的收货地址' );
		}
		if (! $model->delete ()) {
			return $this->jsonFail ( $model, '删除失败' );
		}
		return $this->jsonSuccess ( [ ], '删除成功' );
	}
}
