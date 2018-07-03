<?php

namespace backend\modules\wechat\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_menu}}".
 *
 * @property integer $id
 * @property integer $siteid
 * @property string $url
 * @property string $media_id
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
 */
class WeixinMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id', 'siteid', 'parentid', 'sort', 'target_id', 'jump_type'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['key','media_id'], 'string', 'max' => 100],
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
            'siteid' => 'Siteid',
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
        ];
    }
}
