<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_spec".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $name
 * @property integer $order
 * @property integer $search_index
 * @property integer $image
 */
class Spec extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spec}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['name'], 'required'],
            [['type_id', 'order', 'search_index', 'image'], 'integer'],
            [['name'], 'string', 'max' => 55],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'name' => 'Name',
            'order' => 'Order',
            'search_index' => 'Search Index',
            'image' => 'Image',
        ];
    }
    
    public static function findByid($id) {
    	return static::findOne ( [
    			'id' => $id,
    	] );
    }
}
