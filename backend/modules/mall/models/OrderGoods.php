<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_order_goods".
 *
 * @property string $rec_id
 * @property string $order_id
 * @property string $goods_id
 * @property string $goods_name
 * @property string $goods_sn
 * @property integer $goods_num
 * @property string $market_price
 * @property string $goods_price
 * @property string $cost_price
 * @property string $member_goods_price
 * @property integer $give_integral
 * @property string $spec_key
 * @property string $spec_key_name
 * @property string $bar_code
 * @property integer $is_comment
 * @property integer $prom_type
 * @property integer $prom_id
 * @property integer $is_send
 * @property integer $delivery_id
 * @property string $sku
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'goods_num', 'give_integral', 'is_comment', 'prom_type', 'prom_id', 'is_send', 'delivery_id'], 'integer'],
            [['market_price', 'goods_price', 'cost_price', 'member_goods_price'], 'number'],
            [['goods_name'], 'string', 'max' => 120],
            [['goods_sn'], 'string', 'max' => 60],
            [['spec_key', 'spec_key_name', 'sku'], 'string', 'max' => 128],
            [['bar_code'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'order_id' => 'Order ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'goods_sn' => 'Goods Sn',
            'goods_num' => 'Goods Num',
            'market_price' => 'Market Price',
            'goods_price' => 'Goods Price',
            'cost_price' => 'Cost Price',
            'member_goods_price' => 'Member Goods Price',
            'give_integral' => 'Give Integral',
            'spec_key' => 'Spec Key',
            'spec_key_name' => 'Spec Key Name',
            'bar_code' => 'Bar Code',
            'is_comment' => 'Is Comment',
            'prom_type' => 'Prom Type',
            'prom_id' => 'Prom ID',
            'is_send' => 'Is Send',
            'delivery_id' => 'Delivery ID',
            'sku' => 'Sku',
        ];
    }
}
