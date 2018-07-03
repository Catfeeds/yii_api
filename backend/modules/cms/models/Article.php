<?php

namespace backend\modules\cms\models;

use Yii;

/**
 * This is the model class for table "pre_article".
 *
 * @property integer $id
 * @property integer $site_id
 * @property integer $cat_id
 * @property integer $p_id
 * @property string $title
 * @property integer $user_id
 * @property string $sub
 * @property string $author
 * @property string $remark
 * @property integer $click
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $praise
 * @property integer $tread
 * @property string $thumb
 * @property string $sort_id
 */
class Article extends \yii\db\ActiveRecord {
	const DEFAULT_CAT_ID = 0;
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%article}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'site_id',
								'cat_id',
								'title',
								'thumb' 
						],
						'required' 
				],
				[ 
						[ 
								'id',
								'site_id',
								'cat_id',
								'p_id',
								'user_id',
								'click',
								'status',
								'created_at',
								'updated_at',
								'praise',
								'tread' 
						],
						'integer' 
				],
				[ 
						[ 
								'title',
								'sub',
								'remark' 
						],
						'string' 
				],
				[ 
						[ 
								'author' 
						],
						'string',
						'max' => 30 
				],
				[ 
						[ 
								'thumb' 
						],
						'string',
						'max' => 255 
				],
				[ 
						[ 
								'sort_id' 
						],
						'string',
						'max' => 10 
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'id' => 'ID',
				'site_id' => 'site_id',
				'cat_id' => 'Cat ID',
				'p_id' => 'P ID',
				'title' => 'Title',
				'user_id' => 'User ID',
				'sub' => 'Sub',
				'author' => 'Author',
				'remark' => 'Remark',
				'click' => 'Click',
				'status' => 'Status',
				'created_at' => 'Created At',
				'updated_at' => 'Updated At',
				'praise' => 'Praise',
				'tread' => 'Tread',
				'thumb' => 'Thumb',
				'sort_id' => 'Sort ID' 
		];
	}
	// 店铺内所有文章
	public static function getallbysite_id($site_id) {
		return static::findAll ( [ 
				'site_id' => $site_id 
		] );
	}
	// 展示分类文章
	public static function getallbycatid($catid, $site_id) {
		return static::findAll ( [ 
				'cat_id' => $catid,
				'site_id' => $site_id 
		] );
	}
	// 文章内容详情
	public static function getView($id) {
		return [ 
				static::findOne ( [ 
						'id' => $id 
				] ),
				ArticleData::getOne ( $id ) 
		];
	}
	//
	public static function getOne($id) {
		return static::findOne ( [ 
				'id' => $id 
		] );
	}
}
