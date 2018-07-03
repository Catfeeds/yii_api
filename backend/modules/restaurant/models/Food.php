<?php

namespace backend\modules\restaurant\models;

use Yii;
use phpDocumentor\Reflection\Types\Static_;

/**
 * This is the model class for table "pre_food".
 *
 * @property integer $food_id
 * @property integer $cat_id
 * @property integer $site_id
 * @property string $name
 * @property string $image
 * @property string $content
 * @property string $price
 * @property string $box_price
 * @property integer $store_count
 * @property integer $infinite_count
 * @property integer $order_num
 * @property integer $is_on_sale
 * @property integer $sku
 * @property integer $pro
 * @property integer $is_del
 */
class Food extends \yii\db\ActiveRecord
{
    const IS_ON_SALE = 1;
    const NOT_ON_SALE = 0;
    const IS_DEL = 1;
    const NOT_DEL = 0;
    const IS_INFINITE_COUNT = 1;
    const NOT_INFINITE_COUNT = 0;
    const HAVE_SKU = 1;
    const NO_SKU = 0;
    public $images;
    public $pro;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id','site_id','name','price'], 'required'],
            [['cat_id','site_id', 'store_count', 'infinite_count', 'order_num','is_on_sale','sku','pro'], 'integer'],
            [['content'], 'string'],
            [['price','box_price'], 'number'],
            [['name'], 'string', 'max' => 64],
            [['image'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'food_id' => 'Food ID',
            'cat_id' => 'Cat ID',
            'site_id' => 'Site ID',
            'name' => 'Name',
            'image' => 'Image',
            'content' => 'Content',
            'price' => 'Price',
            'store_count' => 'Store Count',
            'infinite_count' => 'Infinite Count',
            'order_num' => 'Order Num',
            'is_on_sale' => 'Is ON Sale',
            'sku'=>'Sku',
            'pro'=>'Pro'
        ];
    }
    public static function showall($site_id)
    {
        return static::findAll(['site_id'=>$site_id,'is_del'=>static::NOT_DEL]);
    }
    
    public static function showcatid($site_id,$cat_id)
    {
        return static::findAll(['site_id'=>$site_id,'cat_id'=>$cat_id,'is_del'=>static::NOT_DEL]);
    }
    
    public static function showallwithsale($site_id,$sale='')
    {
        if(empty($sale)){
            $sale = static::IS_ON_SALE;
        }
        return static::findAll(['site_id'=>$site_id,'is_on_sale'=>$sale,'is_del'=>static::NOT_DEL]);
    }
    
    public static function showcatidwithsale($site_id,$cat_id,$sale='')
    {
        if(empty($sale)){
            $sale = static::IS_ON_SALE;
        }
        return static::findAll(['site_id'=>$site_id,'cat_id'=>$cat_id,'is_on_sale'=>$sale,'is_del'=>static::NOT_DEL]);
    }
}
