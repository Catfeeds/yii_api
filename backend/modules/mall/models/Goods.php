<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_goods".
 *
 * @property integer $goods_id
 * @property integer $cat_id
 * @property integer $extend_cat_id
 * @property string $goods_sn
 * @property string $goods_name
 * @property integer $click_count
 * @property integer $brand_id
 * @property integer $store_count
 * @property integer $comment_count
 * @property integer $weight
 * @property string $market_price
 * @property string $shop_price
 * @property string $cost_price
 * @property string $price_ladder
 * @property string $keywords
 * @property string $goods_remark
 * @property string $goods_content
 * @property string $original_img
 * @property integer $is_real
 * @property integer $is_on_sale
 * @property integer $is_free_shipping
 * @property integer $on_time
 * @property integer $sort
 * @property integer $is_recommend
 * @property integer $is_new
 * @property integer $is_hot
 * @property integer $last_update
 * @property integer $goods_type
 * @property integer $spec_type
 * @property integer $give_integral
 * @property integer $exchange_integral
 * @property integer $suppliers_id
 * @property integer $sales_sum
 * @property integer $prom_type
 * @property integer $prom_id
 * @property string $commission
 * @property string $spu
 * @property string $sku
 * @property string $shipping_area_ids
 */
class Goods extends \yii\db\ActiveRecord
{
    const HAVE_SKU = 1;
    const NO_SKU = 0;
    const FREE_SHIPPING =1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
    	//return[];
        return [
        	[['cat_id','goods_name','goods_sn','goods_content','market_price', 'shop_price','original_img'],'required'],
            [['cat_id', 'extend_cat_id', 'click_count', 'brand_id', 'store_count', 'comment_count', 'weight', 'is_real', 'is_on_sale', 'is_free_shipping', 'on_time', 'sort', 'is_recommend', 'is_new', 'is_hot', 'last_update', 'goods_type', 'spec_type', 'give_integral', 'exchange_integral', 'suppliers_id', 'sales_sum', 'prom_type', 'prom_id'], 'integer'],
            [['cat_id', 'store_count', 'is_on_sale', 'sort', 'is_new', 'is_hot','goods_type','spec_type', 'give_integral',], 'integer'],
        	[['market_price', 'shop_price', 'cost_price', 'commission'], 'number'],
            [['price_ladder', 'goods_content'], 'string'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['keywords', 'goods_remark', 'original_img', 'shipping_area_ids'], 'string', 'max' => 255],
            [['spu', 'sku'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'cat_id' => 'Cat ID',
            'extend_cat_id' => 'Extend Cat ID',
            'goods_sn' => 'Goods Sn',
            'goods_name' => 'Goods Name',
            'click_count' => 'Click Count',
            'brand_id' => 'Brand ID',
            'store_count' => 'Store Count',
            'comment_count' => 'Comment Count',
            'weight' => 'Weight',
            'market_price' => 'Market Price',
            'shop_price' => 'Shop Price',
            'cost_price' => 'Cost Price',
            'price_ladder' => 'Price Ladder',
            'keywords' => 'Keywords',
            'goods_remark' => 'Goods Remark',
            'goods_content' => 'Goods Content',
            'original_img' => 'Original Img',
            'is_real' => 'Is Real',
            'is_on_sale' => 'Is On Sale',
            'is_free_shipping' => 'Is Free Shipping',
            'on_time' => 'On Time',
            'sort' => 'Sort',
            'is_recommend' => 'Is Recommend',
            'is_new' => 'Is New',
            'is_hot' => 'Is Hot',
            'last_update' => 'Last Update',
            'goods_type' => 'Goods Type',
            'spec_type' => 'Spec Type',
            'give_integral' => 'Give Integral',
            'exchange_integral' => 'Exchange Integral',
            'suppliers_id' => 'Suppliers ID',
            'sales_sum' => 'Sales Sum',
            'prom_type' => 'Prom Type',
            'prom_id' => 'Prom ID',
            'commission' => 'Commission',
            'spu' => 'Spu',
            'sku' => 'Sku',
            'shipping_area_ids' => 'Shipping Area Ids',
        ];
    }
    public function  getCategory(){
    	return $this -> hasone(category::className(),['catid' => 'goods_type']);
    }
    public static function findbyid($goods_id){
        return static::findOne(['goods_id'=>$goods_id]);
    }
}