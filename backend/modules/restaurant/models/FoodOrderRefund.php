<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "{{%food_order_refund}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $order_status
 * @property integer $create_at
 * @property string $note
 * @property integer $status
 * @property integer $user_id
 * @property integer $admin_id
 */
class FoodOrderRefund extends \yii\db\ActiveRecord
{
    const STATUS_NOT_DO = 0;
    const STATUS_AGREE = 1;
    const STATUS_DISAGREE = 2;
    const STATUS_REFUND_PAY = 3;//退款到账
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food_order_refund}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'create_at'], 'required'],
            [['order_id', 'order_status', 'create_at', 'status','user_id','admin_id'], 'integer'],
            [['note'], 'string', 'max' => 255],
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
            'order_status' => 'Order Status',
            'create_at' => 'Create At',
            'note' => 'Note',
            'status' => 'Status',
        ];
    }
}
