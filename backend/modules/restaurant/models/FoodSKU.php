<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_food_sku".
 *
 * @property integer $id
 * @property integer $food_id
 * @property string $name
 * @property string $price
 * @property string $box_price
 * @property integer $store_count
 * @property integer $infinite_count
 */
class FoodSKU extends \yii\db\ActiveRecord
{
    const IS_INFINITE = 1;
    const NOT_INFINITE = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food_sku}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['food_id', 'name','price'], 'required'],
            [['food_id', 'store_count', 'infinite_count'], 'integer'],
            [['price','box_price'], 'number'],
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
            'food_id' => 'Food ID',
            'name' => 'Name',
            'price' => 'Price',
            'store_count' => 'Store Count',
            'infinite_count' => 'Infinite Count',
        ];
    }
    
    public static function showbyfood($food_id)
    {
        return static::findAll(['food_id'=>$food_id]);
    }
    public static function showbyid($id){
        return static::findOne(['id'=>$id]);
    }
}
