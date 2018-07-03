<?php

namespace backend\modules\wechat\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_material_forevermedia}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $title
 * @property integer $siteid
 * @property string $cover_url
 * @property string $media_id
 * @property string $wechat_url
 * @property integer $created_at
 * @property integer $manager_id
 * @property integer $is_use
 */
class Material extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_material_forevermedia}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['siteid', 'created_at', 'manager_id', 'is_use'], 'integer'],
            [['type'], 'string', 'max' => 16],
            [['cover_url', 'wechat_url','title'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'siteid' => 'Siteid',
            'cover_url' => 'Cover Url',
            'media_id' => 'Media ID',
            'wechat_url' => 'Wechat Url',
            'created_at' => 'Created At',
            'manager_id' => 'Manager ID',
            'is_use' => 'Is Use',
        ];
    }
}
