<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_spec_goods_price".
 *
 * @property string $item_id
 * @property integer $goods_id
 * @property string $key
 * @property string $key_name
 * @property string $price
 * @property string $store_count
 * @property string $bar_code
 * @property string $sku
 * @property string $spec_img
 * @property integer $prom_id
 * @property integer $prom_type
 */
class SpecGoodsPrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spec_goods_price}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['goods_id', 'price','store_count','key','key_name'], 'required'],
            [['goods_id', 'store_count', 'prom_id', 'prom_type'], 'integer'],
            [['price'], 'number'],
            [['key', 'key_name', 'spec_img'], 'string', 'max' => 255],
            [['bar_code'], 'string', 'max' => 32],
            [['sku'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => 'Item ID',
            'goods_id' => 'Goods ID',
            'key' => 'Key',
            'key_name' => 'Key Name',
            'price' => 'Price',
            'store_count' => 'Store Count',
            'bar_code' => 'Bar Code',
            'sku' => 'Sku',
            'spec_img' => 'Spec Img',
            'prom_id' => 'Prom ID',
            'prom_type' => 'Prom Type',
        ];
    }
    public static function getbyitemsid($item_id,$goods_id){
    	return static::findOne(['item_id'=>$item_id,'goods_id'=>$goods_id]);
    }
    public static function findByid($id) {
    	return static::findOne ( [
    			'item_id' => $id,
    	] );
    }
    public static function findBykey($key) {
    	return static::findOne ( [
    			'key' => $key,
    	] );
    }
}
