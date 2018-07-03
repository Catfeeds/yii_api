<?php

namespace super\modules\super\models;

use Yii;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "{{%super}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $access_token
 * @property string $password_hash
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Super extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%super}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'access_token', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['id', 'role', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash'], 'string', 'max' => 255],
            [['access_token'], 'string', 'max' => 60],
            [['username'], 'unique'],
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
            'access_token' => 'Access Token',
            'password_hash' => 'Password Hash',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public static function findname($username)
    {
        return static::findOne( ['username' => $username] );
    }
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne ( ['access_token' => $token] );
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
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
}
