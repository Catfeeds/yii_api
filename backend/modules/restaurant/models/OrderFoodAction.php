<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_order_food_action".
 *
 * @property integer $id
 * @property integer $site_id
 * @property integer $order_id
 * @property integer $order_status
 * @property string $action_note
 * @property integer $log_time
 * @property string $order_desc
 * @property integer $category_count
 */
class OrderFoodAction extends \yii\db\ActiveRecord
{
    const ACTION_NOTE_CREAT = '提交订单';
    const ACTION_NOTE_PAY = '订单支付';
    const ACTION_NOTE_FINISH = '订单完成';
    const ACTION_NOTE_CANCEL = '取消订单';
    const ACTION_NOTE_SHIPPING = '订单发货';
    
	const STATUS_DESC_CREAT = '提交订单';
    const STATUS_DESC_CREAT_FINISH = '订单完成';
    const STATUS_DESC_APPLY_REDUNDS= '申请退货';
    const STATUS_DESC_PAY = '支付订单';
    const STATUS_DESC_NOT_PAY = '取消支付';
    const STATUS_DESC_AGREE = '同意退货';
    const STATUS_DESC_DISAGREE = '不同意退货';
    const STATUS_DESC_RETURN_GOODS = '已退货';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_food_action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','site_id', 'order_status', 'action_note', 'log_time'], 'required'],
            [['order_id','site_id', 'order_status', 'log_time'], 'integer'],
            [['action_note'], 'string', 'max' => 64],
            [['order_desc'], 'string', 'max' => 255],
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
            'action_note' => 'Action Note',
            'log_time' => 'Log Time',
            'order_desc' => 'Order Desc',
        ];
    }
    
    public static function showbyorder($order_id)
    {
        return static::findAll(['order_id'=>$order_id]);   
    }
    
}
