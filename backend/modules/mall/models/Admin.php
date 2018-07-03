<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $mobile
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Admin extends \yii\db\ActiveRecord
{
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 10;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
    	return '{{%admin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['mobile','username','access_token', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['role', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
        	[['mobile'], 'string', 'max' => 15],
        	[['mobile'], 'filter', 'filter' => 'trim'],
        	[['mobile'],'match','pattern'=>'/^1[0-9]{10}$/','message'=>'{attribute}必须为1开头的11位纯数字'],
            [['auth_key'], 'string', 'max' => 32],
            [['access_token'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'mobile' => 'Mobile',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    //获取用户名
    public static function showname($user_id)
    {
        return static::find()->select('username')->where('id = :id',[':id'=>$user_id])->one();
    }
    // 查询手机号
    public static function findByMobile($mobile) {
    	return static::findOne (['mobile' => $mobile]);
    }
    
    //用户名查询
    public static function findbyusername($username){
    	return static::find()->select('username,password_hash,access_token,mobile')->where('username=:username',[':username'=>$username])->one();
    }
    
    //用户名和手机号联合查询
    public static function findbyusernameandmobile($username,$mobile){
    	return static::find()->select('username,mobile')->where('username=:username and mobile = :mobile',[':username'=>$username,':mobile'=>$mobile])->one();
    }
    
    //手机号查询
    public static function findOneByMobile($mobile) {
    	return static::findOne ( [
    			'mobile' => $mobile
    	] );
    }
    
    //修改手机号码
    public static function updatemobile($user_id , $mobile)
    {
    	$model = static::findIdentity($user_id);
    	$model -> mobile = $mobile;
    	return $model -> save();
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
    	return static::findOne ( ['access_token' => $token] );
        //throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
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
    
    //AuthKey()
    public function generateAuthKey() {
    	$model = $this->authModel;
    	if (!$model) {
    		$model = new AuthKey();
    		$model->uid = $this->id;
    	}
    	$model->key = Yii::$app->security->generateRandomString(32);
    	$model->save(false);
    }
    
    //实现接口
    public function getAuthKey() {
    	return $this->authModel->key;
    }
}
