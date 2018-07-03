<?php

namespace backend\modules\wechat\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_material_file}}".
 *
 * @property integer $id
 * @property integer $file_id
 * @property string $cover_url
 * @property string $media_id
 * @property string $wechat_url
 * @property integer $cTime
 * @property integer $manager_id
 * @property string $token
 * @property string $title
 * @property integer $type
 * @property string $introduction
 * @property integer $is_use
 * @property integer $aim_id
 * @property string $aim_table
 */
class WeixinMaterialFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_material_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'title'], 'required'],
            [['file_id', 'cTime', 'manager_id', 'type', 'is_use', 'aim_id'], 'integer'],
            [['introduction'], 'string'],
            [['cover_url', 'wechat_url', 'title', 'aim_table'], 'string', 'max' => 255],
            [['media_id', 'token'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_id' => 'File ID',
            'cover_url' => 'Cover Url',
            'media_id' => 'Media ID',
            'wechat_url' => 'Wechat Url',
            'cTime' => 'C Time',
            'manager_id' => 'Manager ID',
            'token' => 'Token',
            'title' => 'Title',
            'type' => 'Type',
            'introduction' => 'Introduction',
            'is_use' => 'Is Use',
            'aim_id' => 'Aim ID',
            'aim_table' => 'Aim Table',
        ];
    }
}
