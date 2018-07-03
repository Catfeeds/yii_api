<?php

/**
 * @date        : 2018年1月29日
 */
namespace super\modules\super\service;

use Yii;
use super\modules\super\models\Super;

class SuperService {
	public static function login($username, $password) {
		$super = Super::findOne ( [ 
				'username' => $username 
		] );
		if (empty ( $super )) {
			return null;
		}
		if (Yii::$app->security->validatePassword ( $password, $super->password_hash )) {
			$super->generateAccessToken ();
			$super->save ();
			return $super->access_token;
		} else {
			return null;
		}
	}
	
	public static function showsuper($offset, $limit) {
		return Super::find ()->select('id , username , role , status , created_at')->orderby ( Super::tableName () . '.created_at DESC' )->offset ( $offset )->limit ( $limit )->all ();
	}
	
	public static function showsupercount() {
		return Super::find ()->orderby ( Super::tableName () . '.created_at DESC' )->count ();
	}
	
	public static function findbyusername($username) {
		return Super::findOne ( [ 
				'username' => $username 
		] );
	}
	public static function findbyid($id) {
		return Super::find ()->select ( 'id , username , role , status , created_at' )->where ( [ 
				'id' => $id 
		] )->one ();
	}
	public static function create($data) {
		$super = new Super ();
		$super->load ( $data, '' );
		$super->password_hash = Yii::$app->security->generatePasswordHash ( $data ['password'] );
		$super->created_at = time ();
		$super->updated_at = time ();
		$super->generateAccessToken ();
		return $super->save ();
	}
	public static function update($data) {
		$super = Super::findOne ( [ 
				'id' => $data ['id'] 
		] );
		$super->password_hash = Yii::$app->security->generatePasswordHash ( $data ['password'] );
		$super->created_at = time ();
		$super->updated_at = time ();
		$super->generateAccessToken ();
		return $super->save ();
	}
}


