<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_user".
 *
 * @property string $id
 * @property integer $contact_id
 * @property string $avater
 * @property string $nickname
 * @property string $username
 * @property string $mobile
 * @property string $password
 * @property string $token
 * @property double $money
 * @property double $score
 * @property integer $status
 * @property string $remark
 * @property string $last_login_ip
 * @property string $autograph
 * @property integer $sex
 * @property integer $identity
 * @property integer $notice_num
 * @property integer $fans_num
 * @property string $access_token
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property integer $last_login_time
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $expires_in
 */
class User extends \yii\db\ActiveRecord
{
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
    	return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['id', 'identity', 'expires_in'], 'required'],
            [['id', 'contact_id', 'status', 'sex', 'identity', 'notice_num', 'fans_num', 'last_login_time', 'created_at', 'updated_at', 'expires_in'], 'integer'],
            [['nickname', 'username', 'password', 'token', 'remark', 'last_login_ip'], 'string'],
            [['money', 'score'], 'number'],
            [['avater'], 'string', 'max' => 200],
            [['mobile'], 'string', 'max' => 15],
        	[['mobile'], 'filter', 'filter' => 'trim'],
        	[['mobile'],'match','pattern'=>'/^1[0-9]{10}$/','message'=>'{attribute}必须为1开头的11位纯数字'],
            [['autograph', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['access_token'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_id' => 'Contact ID',
            'avater' => 'Avater',
            'nickname' => 'Nickname',
            'username' => 'Username',
            'mobile' => 'Mobile',
            'password' => 'Password',
            'token' => 'Token',
            'money' => 'Money',
            'score' => 'Score',
            'status' => 'Status',
            'remark' => 'Remark',
            'last_login_ip' => 'Last Login Ip',
            'autograph' => 'Autograph',
            'sex' => 'Sex',
            'identity' => 'Identity',
            'notice_num' => 'Notice Num',
            'fans_num' => 'Fans Num',
            'access_token' => 'Access Token',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'last_login_time' => 'Last Login Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'expires_in' => 'Expires In',
        ];
    }
    // 查询手机号
    public static function findByMobile($mobile) {
    	return static::findOne ( [
    			'mobile' => $mobile,
    			'status' => self::STATUS_ACTIVE
    	] );
    }
    //查询用户名
    public static function findbyusername($username){
    	return User::find()->select('username,password_hash,access_token')->where('username=:username',[':username'=>$username])->one();
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
				//测试临时使用
				//'id'=>1
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

	
	// 查询手机号
	public static function findOneByMobile($mobile) {
		return static::findOne ( [ 
				'mobile' => $mobile
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
