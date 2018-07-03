<?php

namespace backend\modules\shipping\models;

use Yii;

/**
 * This is the model class for table "pre_delivery_doc".
 *
 * @property string $id
 * @property string $order_id
 * @property string $order_sn
 * @property string $user_id
 * @property string $admin_id
 * @property string $consignee
 * @property string $zipcode
 * @property string $mobile
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $shipping_code
 * @property string $shipping_name
 * @property string $shipping_price
 * @property string $invoice_no
 * @property string $tel
 * @property string $note
 * @property integer $best_time
 * @property integer $create_time
 * @property integer $is_del
 */
class DeliveryDoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%delivery_doc}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'country', 'province', 'city', 'district', 'create_time'], 'required'],
            [['order_id', 'user_id', 'admin_id', 'country', 'province', 'city', 'district', 'best_time', 'create_time', 'is_del'], 'integer'],
            [['shipping_price'], 'number'],
            [['note'], 'string'],
            [['order_sn', 'consignee', 'shipping_name', 'tel'], 'string', 'max' => 64],
            [['zipcode'], 'string', 'max' => 6],
            [['mobile'], 'string', 'max' => 20],
            [['address', 'invoice_no'], 'string', 'max' => 255],
            [['shipping_code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'user_id' => 'User ID',
            'admin_id' => 'Admin ID',
            'consignee' => 'Consignee',
            'zipcode' => 'Zipcode',
            'mobile' => 'Mobile',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'shipping_code' => 'Shipping Code',
            'shipping_name' => 'Shipping Name',
            'shipping_price' => 'Shipping Price',
            'invoice_no' => 'Invoice No',
            'tel' => 'Tel',
            'note' => 'Note',
            'best_time' => 'Best Time',
            'create_time' => 'Create Time',
            'is_del' => 'Is Del',
        ];
    }
}
