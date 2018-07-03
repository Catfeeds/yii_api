<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_goods_category".
 *
 * @property integer $catid
 * @property integer $site_id
 * @property string $name
 * @property integer $parentid
 * @property string $mobile_name
 * @property integer $parent_id
 * @property string $parent_id_path
 * @property integer $level
 * @property integer $sort_order
 * @property integer $is_show
 * @property string $image
 * @property integer $is_hot
 * @property integer $cat_group
 * @property integer $commission_rate
 */
class Category extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%goods_category}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				// [['site_id', 'parentid'], 'required'],
				[ 
						[ 
								'site_id',
								'parentid',
								'level',
								'sort_order',
								'is_show',
								'is_hot',
								'commission_rate' 
						],
						'integer' 
				],
				[ 
						[ 
								'name' 
						],
						'string',
						'max' => 90 
				],
				[ 
						[ 
								'parent_id_path' 
						],
						'string',
						'max' => 128 
				],
				[ 
						[ 
								'image' 
						],
						'string',
						'max' => 512 
				],
				[ 
						[ 
								'catid' 
						],
						'integer' 
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'catid' => 'Catid',
				'site_id' => 'site_id',
				'name' => 'Name',
				'parentid' => 'Parentid',
				'parent_id_path' => 'Parent Id Path',
				'level' => 'Level',
				'sort_order' => 'Sort Order',
				'is_show' => 'Is Show',
				'image' => 'Image',
				'is_hot' => 'Is Hot',
				'commission_rate' => 'Commission Rate' 
		];
	}
}
