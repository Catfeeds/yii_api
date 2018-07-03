<?php

namespace backend\modules\ucenter\models;

use Yii;

/**
 * This is the model class for table "pre_weixin_user".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $site_id
 * @property string $headimgurl
 * @property integer $create_time
 * @property integer $web_expires
 * @property string $qr
 * @property string $openid
 * @property string $nickname
 */
class WeixinUser extends \yii\db\ActiveRecord
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
            [['uid', 'create_time', 'openid', 'nickname'], 'required'],
            [['id', 'uid','site_id', 'create_time', 'web_expires'], 'integer'],
            [['headimgurl'], 'string', 'max' => 255],
            [['qr'], 'string', 'max' => 200],
            [['openid', 'nickname'], 'string', 'max' => 100],
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
            'headimgurl' => 'Headimgurl',
            'create_time' => 'Create Time',
            'web_expires' => 'Web Expires',
            'qr' => 'Qr',
            'openid' => 'Openid',
            'nickname' => 'Nickname',
        ];
    }
    public static function findByOpenid($openid)
    {
        return static::findOne(['openid' => $openid]);
    }
}
