<?php

namespace backend\modules\wechat\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_material_image}}".
 *
 * @property integer $id
 * @property integer $cover_id
 * @property string $cover_url
 * @property string $media_id
 * @property string $wechat_url
 * @property integer $cTime
 * @property integer $manager_id
 * @property integer $is_use
 * @property integer $aim_id
 * @property string $aim_table
 */
class WeixinMaterialImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_material_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
        return [
            [['cover_id', 'cTime', 'manager_id', 'is_use', 'aim_id'], 'integer'],
            [['cover_url', 'wechat_url', 'aim_table'], 'string', 'max' => 255],
            [['media_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cover_id' => 'Cover ID',
            'cover_url' => 'Cover Url',
            'media_id' => 'Media ID',
            'wechat_url' => 'Wechat Url',
            'cTime' => 'C Time',
            'manager_id' => 'Manager ID',
            'is_use' => 'Is Use',
            'aim_id' => 'Aim ID',
            'aim_table' => 'Aim Table',
            'attribute' => 'attribute'
        ];
    }
}
