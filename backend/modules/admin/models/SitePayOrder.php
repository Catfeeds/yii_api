<?php

namespace backend\modules\admin\models;

use Yii;

/**
 * This is the model class for table "{{%site_pay_order}}".
 *
 * @property integer $id
 * @property string $order_sn
 * @property integer $admin
 * @property integer $site
 * @property integer $time
 * @property string $price
 * @property integer $create_at
 * @property integer $order_status
 * @property integer $pay_status
 * @property integer $pay_code
 */
class SitePayOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_pay_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'admin', 'site', 'price', 'create_at', 'pay_code'], 'required'],
            [['admin', 'site', 'time', 'create_at', 'order_status', 'pay_status', 'pay_code'], 'integer'],
            [['price'], 'number'],
            [['order_sn'], 'string', 'max' => 128],
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
            'admin' => 'Admin',
            'site' => 'Site',
            'time' => 'Time',
            'price' => 'Price',
            'create_at' => 'Create At',
            'order_status' => 'Order Status',
            'pay_status' => 'Pay Status',
            'pay_code' => 'Pay Code',
        ];
    }
}
