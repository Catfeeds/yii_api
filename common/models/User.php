<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use backend\modules\ucenter\models\Weixin_user;

/**
 * User model
 *
 * @property integer $id
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface {
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 10;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%user}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [ 
				TimestampBehavior::className () 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[['mobile'], 'filter', 'filter' => 'trim'],
				[['mobile'],'match','pattern'=>'/^1[0-9]{10}$/','message'=>'{attribute}必须为1开头的11位纯数字'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id) {
		return static::findOne ( [ 
				'id' => $id,
				'status' => self::STATUS_ACTIVE 
		] );
	}
	
	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null) {
		return static::findOne ( [
				'access_token' => $token 
		] );
		// throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
	}
	//修改密码
	public static function updatemobile($user_id , $mobile)
	{
		$model = static::findIdentity($user_id);
		$model -> mobile = $mobile;
		return $model -> save();
	}
	/**
	 * Finds user by username
	 *
	 * @param string $username        	
	 * @return static|null
	 */
	public static function findByUsername($username) {
		return static::findOne ( [ 
				'username' => $username,
				'status' => self::STATUS_ACTIVE 
		] );
	}
	
	// 查询手机号
	public static function findByMobile($mobile) {
		return static::findOne ( [ 
				'mobile' => $mobile,
				'status' => self::STATUS_ACTIVE 
		] );
	}
	// 查询手机号
	public static function findOneByMobile($mobile) {
		return static::findOne ( [ 
				'mobile' => $mobile,
				'status' => self::STATUS_ACTIVE 
		] );
	}
	/**
	 * Finds user by password reset token
	 *
	 * @param string $token
	 *        	password reset token
	 * @return static|null
	 */
	public static function findByPasswordResetToken($token) {
		if (! static::isPasswordResetTokenValid ( $token )) {
			return null;
		}
		
		return static::findOne ( [ 
				'password_reset_token' => $token,
				'status' => self::STATUS_ACTIVE 
		] );
	}
	
	/**
	 * Finds out if password reset token is valid
	 *
	 * @param string $token
	 *        	password reset token
	 * @return boolean
	 */
	public static function isPasswordResetTokenValid($token) {
		if (empty ( $token )) {
			return false;
		}
		
		$timestamp = ( int ) substr ( $token, strrpos ( $token, '_' ) + 1 );
		$expire = Yii::$app->params ['user.passwordResetTokenExpire'];
		return $timestamp + $expire >= time ();
	}
	
	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->getPrimaryKey ();
	}
	
	/**
	 * @inheritdoc
	 */
	public function getAuthKey() {
		return $this->auth_key;
	}
	
	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey) {
		return $this->getAuthKey () === $authKey;
	}
	
	/**
	 * Validates password
	 *
	 * @param string $password
	 *        	password to validate
	 * @return boolean if password provided is valid for current user
	 */
	public function validatePassword($password) {
		return Yii::$app->security->validatePassword ( $password, $this->password_hash );
	}
	
	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param string $password        	
	 */
	public function setPassword($password) {
		$this->password_hash = Yii::$app->security->generatePasswordHash ( $password );
	}
	
	/**
	 * Generates "remember me" authentication key
	 */
	public function generateAuthKey() {
		$this->auth_key = Yii::$app->security->generateRandomString ();
	}
	
	/**
	 * Generates new password reset token
	 */
	public function generatePasswordResetToken() {
		$this->password_reset_token = Yii::$app->security->generateRandomString () . '_' . time ();
	}
	//找回密码等使用
	public static function findbyusernameandmobile($username,$mobile){
		return static::find()->select('username,mobile')->where('username=:username and mobile = :mobile',[':username'=>$username,':mobile'=>$mobile])->one();
	}
	/**
	 * Removes password reset token
	 */
	public function removePasswordResetToken() {
		$this->password_reset_token = null;
	}
	public function generateAccessToken() {
		$this->access_token = Yii::$app->security->generateRandomString ();
	}
	public function getAccessToken() {
		return $this->access_token;
	}
	public function loginByAccessToken($accessToken, $type) {
		// 查询数据库中有没有存在这个token
		return static::findIdentityByAccessToken ( $accessToken, $type );
	}
	// 关联表查询用户详细信息
	public function getUser() {
		return $this->hasOne ( Weixin_user::className (), [ 
				'uid' => 'id' 
		] );
	}
}
