<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_food_order".
 *
 * @property integer $order_id
 * @property string $order_sn
 * @property string $other_sn
 * @property integer $user_id
 * @property integer $site_id
 * @property integer $table_id
 * @property integer $people
 * @property integer $create_at
 * @property integer $update_at
 * @property integer $order_status
 * @property string $order_price
 * @property integer $parent_id
 * @property integer $is_delete
 * @property integer $is_out
 * @property integer $deliverytime
 * @property string $box_price
 * @property string $shipping_price
 * @property string $note
 * @public string $sumMoney
 * @public string $refund
 */
class FoodOrder extends \yii\db\ActiveRecord
{
    const ORDER_STATUS_CANCEL = -3; //取消订单
    const ORDER_STATUS_PAY_OVERTIME = -2; //支付超时
    const ORDER_STATUS_ERROR = -1;//订单出错
    
    const ORDER_STATUS_CREATE = 0; //订单创建
    const ORDER_STATUS_PAY = 1; //订单支付
    const ORDER_STATUS_GETORDER = 2; //已接单
    const ORDER_STATUS_SHIPPING = 3; //订单配送中
    const ORDER_STATUS_FINISH = 4; //订单完成
    const ORDER_STATUS_REDUNDS = 7; //订单退货申请中
    const ORDER_STATUS_AGREE_REFUNDS = 8;//退货同意,退款中
    const ORDER_STATUS_DISAGREE_REFUNDS = 9;//不同意退货
    const ORDER_STATUS_REFUNDS_END = 10;//退款到账
    
    const ORDER_STATUS_APPLY_REDUNDS= 8; 
    const ORDER_STATUS_DISAGREE = 9; 
    
    const ORDER_IS_OUT = 1;
    const ORDER_NOT_OUT = 0;
    
    public $sumMoney;
    public $order_count;
    public $refund;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food_order}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'user_id', 'site_id', 'table_id', 'order_status', 'order_price'], 'required'],
            [['user_id', 'site_id', 'table_id', 'create_at', 'update_at', 'order_status','people', 'parent_id', 'is_delete',], 'integer'],
            [['order_price','box_price','shipping_price'], 'number'],
            [['note','other_sn'], 'string'],
            [['order_sn'], 'string', 'max' => 32],
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
            'site_id' => 'Site ID',
        	'table_id' => 'Table ID',
            'people'=>'People',
            'create_at' => 'Create At',
            'order_status' => 'Order Status',
            'order_price' => 'Order Price',
            'parent_id' => 'Parent ID',
            'is_delete' => 'Is Delete',
        	'is_out' => 'Is Out',
        	'deliverytime' => 'Deliverytime',
        	'box_price'=> 'Box Price',
            'note' => 'Note',
        ];
    }
    
    public static function findbyid($order_id)
    {
        return static::findOne(['order_id'=>$order_id]);
    }
    //子菜单
    public static function findallbyid($parent_id)
    {
        return static::findAll(['parent_id'=>$parent_id]);
    }
}
