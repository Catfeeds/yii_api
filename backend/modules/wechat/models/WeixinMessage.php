<?php

namespace backend\modules\wechat\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_message}}".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $ToUserName
 * @property string $FromUserName
 * @property integer $CreateTime
 * @property string $MsgType
 * @property string $MsgId
 * @property string $Content
 * @property string $PicUrl
 * @property string $MediaId
 * @property string $Format
 * @property string $ThumbMediaId
 * @property string $Title
 * @property string $Description
 * @property string $Url
 * @property integer $collect
 * @property integer $deal
 * @property integer $is_read
 * @property integer $type
 * @property integer $is_material
 */
class WeixinMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id'], 'required'],
            [['site_id', 'CreateTime', 'collect', 'deal', 'is_read', 'type', 'is_material'], 'integer'],
            [['Content', 'Description'], 'string'],
            [['ToUserName', 'FromUserName', 'MsgId', 'MediaId', 'Title'], 'string', 'max' => 100],
            [['MsgType', 'Format', 'ThumbMediaId'], 'string', 'max' => 30],
            [['PicUrl', 'Url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'ToUserName' => 'To User Name',
            'FromUserName' => 'From User Name',
            'CreateTime' => 'Create Time',
            'MsgType' => 'Msg Type',
            'MsgId' => 'Msg ID',
            'Content' => 'Content',
            'PicUrl' => 'Pic Url',
            'MediaId' => 'Media ID',
            'Format' => 'Format',
            'ThumbMediaId' => 'Thumb Media ID',
            'Title' => 'Title',
            'Description' => 'Description',
            'Url' => 'Url',
            'collect' => 'Collect',
            'deal' => 'Deal',
            'is_read' => 'Is Read',
            'type' => 'Type',
            'is_material' => 'Is Material',
        ];
    }
}
