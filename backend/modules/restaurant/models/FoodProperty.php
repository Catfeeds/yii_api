<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_food_property".
 *
 * @property integer $property_id
 * @property integer $food_id
 * @property string $name
 * @property string $child
 */
class FoodProperty extends \yii\db\ActiveRecord
{
	public $child;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food_property}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['food_id', 'name'], 'required'],
            [['food_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['child'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'property_id' => 'Property ID',
            'food_id' => 'Food ID',
            'name' => 'Name',
            'child'=>'Child',
        ];
    }
    public function setchild($child)
    {
    	$this->child = $child;
    }
    public static function showbyfoodid($food_id){
        return static::findAll(['food_id' => $food_id]);
    }
    public static function showproid($property_id){
        return static::findOne(['property_id'=>$property_id]);
    }
    public static function showthis($food_id){
        $models = static::findAll(['food_id' => $food_id]);
        foreach ($models as $i => $model){
            $models[$i]['child'] = FoodPropertychild::findAll(['property_id'=>$model['property_id']]);
        }
        return $models;
    }
}
