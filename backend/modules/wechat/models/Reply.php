<?php

namespace backend\modules\wechat\models;

use Yii;

/**
 * This is the model class for table "pre_weixin_reply".
 *
 * @property integer $id
 * @property string $url
 * @property string $key
 * @property string $name
 * @property integer $parentid
 * @property integer $sort
 * @property string $type
 * @property string $from_type
 * @property string $addon
 * @property integer $target_id
 * @property string $sucai_type
 * @property integer $jump_type
 * @property integer $siteid
 */
class Reply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_reply}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
        return [
            [['parentid', 'sort', 'target_id', 'jump_type', 'siteid'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['key'], 'string', 'max' => 100],
            [['name', 'from_type', 'sucai_type'], 'string', 'max' => 50],
            [['type', 'addon'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'key' => 'Key',
            'name' => 'Name',
            'parentid' => 'Parentid',
            'sort' => 'Sort',
            'type' => 'Type',
            'from_type' => 'From Type',
            'addon' => 'Addon',
            'target_id' => 'Target ID',
            'sucai_type' => 'Sucai Type',
            'jump_type' => 'Jump Type',
            'siteid' => 'Siteid',
        ];
    }
}
