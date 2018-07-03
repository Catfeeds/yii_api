<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "{{%wx_refund}}".
 *
 * @property integer $id
 * @property string $order_sn
 * @property string $other_sn
 * @property string $refund_no
 * @property integer $order_price
 * @property integer $refund_price
 * @property integer $status
 * @property integer $create_at
 * @property integer $user_id
 */
class WxRefund extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wx_refund}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'other_sn','refund_no','order_price','refund_price','create_at'], 'required'],
            [['order_price', 'refund_price', 'status', 'create_at', 'user_id'], 'integer'],
            [['order_sn','refund_no'], 'string', 'max' => 32],
            [['other_sn'], 'string', 'max' => 64],
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
            'other_sn' => 'Other Sn',
            'order_price' => 'Order Price',
            'refund_price' => 'Refund Price',
            'status' => 'Status',
            'create_at' => 'Create At',
            'user_id' => 'User ID',
        ];
    }
}
