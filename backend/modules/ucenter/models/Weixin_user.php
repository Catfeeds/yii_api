<?php

namespace backend\modules\ucenter\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_user}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $avatar
 * @property integer $create_time
 * @property integer $web_expires
 * @property string $qr
 */
class Weixin_user extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'avatar', 'create_time', 'web_expires'], 'required'],
            [['uid', 'create_time', 'web_expires',], 'integer'],
            [['avatar','openid'], 'string', 'max' => 255],
            [['qr'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'avatar' => 'Avatar',
            'create_time' => 'Create Time',
            'web_expires' => 'Web Expires',
            'qr' => 'Qr',
        ];
    }
    
    public static function findByOpenid($openid)
    {
        return static::findOne(['openid' => $openid]);
    }
    
}
