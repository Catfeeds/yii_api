<?php
/**
 * @author Jason
 * @copyright Copyright © 2016年
 */

namespace common\models;

use Yii;
use common\utils\StringUtil;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $order_id
 * @property string $order_sn
 * @property integer $user_id
 * @property integer $order_status
 * @property integer $shipping_status
 * @property integer $pay_status
 * @property string $consignee
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property integer $twon
 * @property string $address
 * @property string $zipcode
 * @property string $mobile
 * @property string $email
 * @property string $shipping_code
 * @property string $shipping_name
 * @property string $pay_code
 * @property string $pay_name
 * @property string $invoice_title
 * @property string $goods_price
 * @property string $shipping_price
 * @property string $user_money
 * @property string $coupon_price
 * @property integer $integral
 * @property string $integral_money
 * @property string $order_amount
 * @property string $total_amount
 * @property integer $add_time
 * @property integer $shipping_time
 * @property integer $confirm_time
 * @property integer $pay_time
 * @property integer $order_prom_type
 * @property integer $order_prom_id
 * @property string $order_prom_amount
 * @property string $discount
 * @property string $user_note
 * @property string $admin_note
 * @property string $parent_sn
 * @property integer $is_distribut
 * @property string $paid_money
 */
class Order extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'order_status', 'shipping_status', 'pay_status', 'country', 'province', 'city', 'district', 'twon', 'integral', 'add_time', 'shipping_time', 'confirm_time', 'pay_time', 'order_prom_type', 'order_prom_id', 'is_distribut'], 'integer'],
            [['goods_price', 'shipping_price', 'user_money', 'coupon_price', 'integral_money', 'order_amount', 'total_amount', 'order_prom_amount', 'discount', 'paid_money'], 'number'],
            [['order_sn'], 'string', 'max' => 20],
            [['consignee', 'zipcode', 'mobile', 'email'], 'string', 'max' => 60],
            [['address', 'user_note', 'admin_note'], 'string', 'max' => 255],
            [['shipping_code', 'pay_code'], 'string', 'max' => 32],
            [['shipping_name', 'pay_name'], 'string', 'max' => 120],
            [['invoice_title'], 'string', 'max' => 256],
            [['parent_sn'], 'string', 'max' => 100],
            [['order_sn'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'user_id' => 'User ID',
            'order_status' => 'Order Status',
            'shipping_status' => 'Shipping Status',
            'pay_status' => 'Pay Status',
            'consignee' => 'Consignee',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'twon' => 'Twon',
            'address' => 'Address',
            'zipcode' => 'Zipcode',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'shipping_code' => 'Shipping Code',
            'shipping_name' => 'Shipping Name',
            'pay_code' => 'Pay Code',
            'pay_name' => 'Pay Name',
            'invoice_title' => 'Invoice Title',
            'goods_price' => 'Goods Price',
            'shipping_price' => 'Shipping Price',
            'user_money' => 'User Money',
            'coupon_price' => 'Coupon Price',
            'integral' => 'Integral',
            'integral_money' => 'Integral Money',
            'order_amount' => 'Order Amount',
            'total_amount' => 'Total Amount',
            'add_time' => 'Add Time',
            'shipping_time' => 'Shipping Time',
            'confirm_time' => 'Confirm Time',
            'pay_time' => 'Pay Time',
            'order_prom_type' => 'Order Prom Type',
            'order_prom_id' => 'Order Prom ID',
            'order_prom_amount' => 'Order Prom Amount',
            'discount' => 'Discount',
            'user_note' => 'User Note',
            'admin_note' => 'Admin Note',
            'parent_sn' => 'Parent Sn',
            'is_distribut' => 'Is Distribut',
            'paid_money' => 'Paid Money'
        ];
    }
}
