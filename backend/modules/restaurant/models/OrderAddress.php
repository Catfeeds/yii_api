<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "{{%order_address}}".
 *
 * @property integer $id
 * @property string $order_sn
 * @property string $consignee
 * @property string $mobile
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property integer $twon
 * @property string $address
 * @property integer $shipping_code
 * @property string $shipping_msg
 */
class OrderAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_address}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'consignee', 'mobile', 'country', 'province', 'city', 'district', 'twon', 'address'], 'required'],
            [['country', 'province', 'city', 'district', 'twon', 'shipping_code'], 'integer'],
            [['order_sn', 'consignee'], 'string', 'max' => 32],
            [['mobile'], 'string', 'max' => 11],
            [['shipping_msg','address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => 'Order Sn',
            'consignee' => 'Consignee',
            'mobile' => 'Mobile',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'twon' => 'Twon',
            'address' => 'Address',
            'shipping_code' => 'Shipping Code',
            'shipping_msg' => 'Shipping Msg',
        ];
    }
}
