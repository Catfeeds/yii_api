<?php

namespace backend\modules\mall\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "pre_order".
 *
 * @property string $order_id
 * @property string $order_sn
 * @property string $user_id
 * @property integer $order_status
 * @property integer $shipping_status
 * @property integer $pay_status
 * @property string $consignee
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $district
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
 * @property string $integral
 * @property string $integral_money
 * @property string $order_amount
 * @property string $total_amount
 * @property string $add_time
 * @property integer $shipping_time
 * @property integer $confirm_time
 * @property string $pay_time
 * @property string $transaction_id
 * @property integer $order_prom_type
 * @property integer $order_prom_id
 * @property string $order_prom_amount
 * @property string $discount
 * @property string $user_note
 * @property string $admin_note
 * @property string $parent_sn
 * @property integer $is_distribut
 * @property string $paid_money
 * @property integer $deleted
 */
class Order extends \yii\db\ActiveRecord {
    const ORDER_STATUS_ACTIVE = 0; 
	const ORDER_STATUS_FINISH = 1;
	const PAY_STATUS_ACTIVE = 0;
	const PAY_STATUS_FINISH= 1;
	const SHIPPING_STATUS_ACTIVE =0;
	const SHIPPING_STATUS_FINISH = 1;
	const STATUS_PAY_NAME = 1;
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%order}}';
	}
	public $add_end_time;
	public $shipping_end_time;
	public $confirm_end_time;
	public $pay_end_time;
	public $goods_max_price;
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'user_id',
								'order_status',
								'shipping_status',
								'pay_status',
								'country',
								'province',
								'city',
								'district',
								'twon',
								'integral',
								'add_time',
								'shipping_time',
								'confirm_time',
								'pay_time',
						        'add_end_time',
						        'shipping_end_time',
						        'confirm_end_time',
						        'pay_end_time',
								'order_prom_type',
								'order_prom_id',
								'is_distribut',
								'deleted' ,
						],
						'integer' 
				],
				[ 
						[ 
								'goods_price',
						        'goods_max_price',
								'shipping_price',
								'user_money',
								'coupon_price',
								'integral_money',
								'order_amount',
								'total_amount',
								'order_prom_amount',
								'discount',
								'paid_money' 
						],
						'number' 
				],
				[ 
						[ 
								'order_sn' 
						],
						'string',
						'max' => 32 
				],
				[ 
						[ 
								'consignee',
								'zipcode',
								'mobile',
								'email' 
						],
						'string',
						'max' => 60 
				],
				[ 
						[ 
								'address',
								'transaction_id',
								'user_note',
								'admin_note' 
						],
						'string',
						'max' => 255 
				],
				[ 
						[ 
								'shipping_code',
								'pay_code' 
						],
						'string',
						'max' => 32 
				],
				[ 
						[ 
								'shipping_name',
								'pay_name' 
						],
						'string',
						'max' => 120 
				],
				[ 
						[ 
								'invoice_title' 
						],
						'string',
						'max' => 256 
				],
				[ 
						[ 
								'parent_sn' 
						],
						'string',
						'max' => 100 
				]
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
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
				'transaction_id' => 'Transaction ID',
				'order_prom_type' => 'Order Prom Type',
				'order_prom_id' => 'Order Prom ID',
				'order_prom_amount' => 'Order Prom Amount',
				'discount' => 'Discount',
				'user_note' => 'User Note',
				'admin_note' => 'Admin Note',
				'parent_sn' => 'Parent Sn',
				'is_distribut' => 'Is Distribut',
				'paid_money' => 'Paid Money',
				'deleted' => 'Deleted' 
		];
	}
	public function searchcount($params){
	    $this->load($params,'');
	    if (!$this->validate()) {
	        return null;
	    }
	    return static::find()->andFilterWhere([
	            'user_id' => $this->user_id,
	            'order_sn' => $this->order_sn,
	            'order_status' => $this->order_status,
	            'shipping_status' => $this->shipping_status,
	            'pay_status' => $this->pay_status,
	            'mobile'=>$this->mobile,
	            'shipping_code'=>$this->shipping_code,
	            'province' =>$this->province,
	            'city' => $this->city,
	            'district' => $this->district,
	            'twon' => $this->twon,
	    ])
	    ->andFilterWhere(['like','consignee',$this->consignee])
	    ->andFilterWhere(['like','address',$this->address])
	    ->andFilterWhere(['between','add_time',$this->add_time,$this->add_end_time])
	    ->andFilterWhere(['between','shipping_time',$this->shipping_time,$this->shipping_end_time])
	    ->andFilterWhere(['between','pay_time',$this->pay_time,$this->pay_end_time])
	    ->andFilterWhere(['between','confirm_time',$this->confirm_time,$this->confirm_end_time])
	    ->andFilterWhere(['between','goods_price',$this->goods_price,$this->goods_max_price])
	    ->count();
	}
	public function search($params,$offset,$limit)
	{
	    $this->load($params,'');
	    if (!$this->validate()) {
	        return null;
	    }
	    return static::find()->andFilterWhere([
	            'user_id' => $this->user_id,
	            'order_sn' => $this->order_sn,
	            'order_status' => $this->order_status,
	            'pay_status' => $this->pay_status,
	            'shipping_status' => $this->shipping_status,
	            'mobile'=>$this->mobile,
	            'shipping_code'=>$this->shipping_code,
	            'province' =>$this->province,
	            'city' => $this->city,
	            'district' => $this->district,
	            'twon' => $this->twon,
	    ])
	    ->andFilterWhere(['like','consignee',$this->consignee])
	    ->andFilterWhere(['like','address',$this->address])
	    ->andFilterWhere(['between','add_time',$this->add_time,$this->add_end_time])
	    ->andFilterWhere(['between','shipping_time',$this->shipping_time,$this->shipping_end_time])
	    ->andFilterWhere(['between','pay_time',$this->pay_time,$this->pay_end_time])
	    ->andFilterWhere(['between','confirm_time',$this->confirm_time,$this->confirm_end_time])
	    ->andFilterWhere(['between','goods_price',$this->goods_price,$this->goods_max_price])
	    ->offset ( $offset )
	    ->limit ( $limit )
	    ->all();
	}
}

