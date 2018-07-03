<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_food_propertychild".
 *
 * @property integer $id
 * @property integer $property_id
 * @property string $name
 */
class FoodPropertychild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food_propertychild}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_id', 'name'], 'required'],
            [['property_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'property_id' => 'Property ID',
            'name' => 'Name',
        ];
    }
    public static function showbyproid($id){
        return static::findAll(['property_id'=>$id]);
    }
    public static function showproid($id){
        return static::findOne(['id'=>$id]);
    }
}
