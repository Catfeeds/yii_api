<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_order_food".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $name
 * @property integer $order_id
 * @property integer $food_id
 * @property integer $num
 * @property string $sku_name
 * @property string $pro_name
 * @property integer $finish_num
 * @property integer $sku_id
 * @property integer $property_id
 * @property string $price
 * @property string $box_price
 * @property string $note
 */
class OrderFood extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_food}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'name', 'order_id', 'food_id', 'price'], 'required'],
            [['site_id', 'order_id', 'food_id', 'num', 'finish_num', 'sku_id', 'property_id'], 'integer'],
            [['price','box_price'], 'number'],
            [['note'], 'string'],
            [['name', 'sku_name'], 'string', 'max' => 64],
            [['pro_name'], 'string', 'max' => 128],
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
            'name' => 'Name',
            'order_id' => 'Order ID',
            'food_id' => 'Food ID',
            'num' => 'Num',
            'sku_name' => 'Sku Name',
            'pro_name' => 'Pro Name',
            'finish_num' => 'Finish Num',
            'sku_id' => 'Sku ID',
            'property_id' => 'Property ID',
            'price' => 'Price',
            'note' => 'Note',
        ];
    }
}
