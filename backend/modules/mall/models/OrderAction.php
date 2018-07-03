<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_order_action".
 *
 * @property string $action_id
 * @property string $order_id
 * @property integer $action_user
 * @property integer $order_status
 * @property integer $shipping_status
 * @property integer $pay_status
 * @property string $action_note
 * @property string $log_time
 * @property string $status_desc
 */
class OrderAction extends \yii\db\ActiveRecord
{
	const STATUS_DESC_CREAT = '提交订单';
	const STATUS_DESC_CREAT_FINISH = '订单完成';
	const STATUS_DESC_TOKEN_OVER = '订单收货';
	const STATUS_DESC_PAY = '支付订单';
	const STATUS_DESC_NOT_PAY = '取消支付';
	const STATUS_DESC_PAY_FINISH = '付款完成';
	
// 	const SHOPING_STATUS_TOKEN_OVER = '收货';
// 	const SHOPING_STATUS_TOKEN_ING = '配送';
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'action_user', 'order_status', 'shipping_status', 'pay_status', 'log_time'], 'integer'],
            [['action_note', 'status_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'action_id' => 'Action ID',
            'order_id' => 'Order ID',
            'action_user' => 'Action User',
            'order_status' => 'Order Status',
            'shipping_status' => 'Shipping Status',
            'pay_status' => 'Pay Status',
            'action_note' => 'Action Note',
            'log_time' => 'Log Time',
            'status_desc' => 'Status Desc',
        ];
    }
    
    public static function findbyid($order_id)
    {
    	return static::findOne(['order_id'=>$order_id]);
    }
}
