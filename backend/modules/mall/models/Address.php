<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_user_address".
 *
 * @property string $address_id
 * @property string $user_id
 * @property string $consignee
 * @property string $email
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property integer $twon
 * @property string $address
 * @property string $zipcode
 * @property string $mobile
 * @property integer $is_default
 * @property integer $is_pickup
 */
class Address extends \yii\db\ActiveRecord {
	const STATUS_IS_DEFAULT = 1;
	const STATUS_NOT_DEFAULT = 0;
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%user_address}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'user_id',
								'mobile',
								'consignee',
								'address',
								'country',
								'province',
								'city',
								'district',
								'twon' 
						],
						'required' 
				],
				[ 
						[ 
								'user_id',
								'country',
								'province',
								'city',
								'district',
								'twon',
								'is_default',
								'is_pickup' 
						],
						'integer' 
				],
				[ 
						[ 
								'consignee',
								'email',
								'zipcode',
								'mobile' 
						],
						'string',
						'max' => 60 
				],
				[ 
						[ 
								'address' 
						],
						'string',
						'max' => 120 
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'address_id' => 'Address ID',
				'user_id' => 'User ID',
				'consignee' => 'Consignee',
				'email' => 'Email',
				'country' => 'Country',
				'province' => 'Province',
				'city' => 'City',
				'district' => 'District',
				'twon' => 'Twon',
				'address' => 'Address',
				'zipcode' => 'Zipcode',
				'mobile' => 'Mobile',
				'is_default' => 'Is Default',
				'is_pickup' => 'Is Pickup' 
		];
	}
	/**
	 * 获取默认的地址
	 * 
	 * @param
	 *        	$user_id
	 * @return null|static
	 */
	public static function findDefault($user_id) {
		return static::findAll ( [ 
				'user_id' => $user_id,
				'is_default' => static::STATUS_IS_DEFAULT 
		] );
	}
	
	/**
	 * 获取用户地址列表
	 */
	public static function findByUser($user_id) {
		$models = static::find ()->where ( 'user_id=:user_id ', [ 
				'user_id' => $user_id 
		] )->orderBy ( 'is_default desc' )->all ();
		return $models;
	}
	public static function findByUserArray($user_id) {
	    $models = static::find ()->where ( 'user_id=:user_id ', [
	        'user_id' => $user_id
	    ] )->orderBy ( 'is_default desc' )->all ();
	    foreach ($models as $i=>$model)
	    {
	        
	        $array = Region::getRegionsarray($model['twon']);
	        $array['address'] = $model['address'];
	        $models[$i]['address'] = $array;
	        
	    }
	    return $models;
	}
	public static function findByUserCount($user_id) {
		return static::find ()->where ( 'user_id=:user_id ', [ 
				'user_id' => $user_id 
		] )->orderBy ( 'is_default desc' )->count ();
	}
	/**
	 * 获取具体地址
	 */
	public static function findTheAddress($user_id, $address_id) {
		return static::find ()->where ( 'user_id=:user_id and address_id=:address_id', [ 
				':user_id' => $user_id,
				':address_id' => $address_id 
		] )->one ();
	}
	
	public static function findTheAddressArray($user_id, $address_id) {
	    return static::find ()->where ( 'user_id=:user_id and address_id=:address_id', [
	        ':user_id' => $user_id,
	        ':address_id' => $address_id
	    ] )->asArray()->one ();
	}
}
