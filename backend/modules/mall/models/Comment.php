<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_comment".
 *
 * @property string $comment_id
 * @property string $goods_id
 * @property string $email
 * @property string $username
 * @property string $content
 * @property string $add_time
 * @property string $ip_address
 * @property integer $is_show
 * @property string $parent_id
 * @property string $user_id
 * @property string $img
 * @property integer $order_id
 * @property integer $deliver_rank
 * @property integer $goods_rank
 * @property integer $service_rank
 * @property integer $zan_num
 * @property string $zan_userid
 * @property integer $is_anonymous
 */
class Comment extends \yii\db\ActiveRecord {
	const NOT_COMMENT = 0;
	const IS_COMMENT = 1;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%comment}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'content',
								'deliver_rank',
								'goods_rank',
								'service_rank' 
						],
						'required' 
				],
				[ 
						[ 
								'goods_id',
								'add_time',
								'is_show',
								'parent_id',
								'user_id',
								'order_id',
								'deliver_rank',
								'goods_rank',
								'service_rank',
								'zan_num',
								'is_anonymous' 
						],
						'integer' 
				],
				[ 
						[ 
								'content' 
						],
						'required' 
				],
				[ 
						[ 
								'content',
								'img' 
						],
						'string' 
				],
				[ 
						[ 
								'email',
								'username' 
						],
						'string',
						'max' => 60 
				],
				[ 
						[ 
								'ip_address' 
						],
						'string',
						'max' => 15 
				],
				[ 
						[ 
								'zan_userid' 
						],
						'string',
						'max' => 255 
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'comment_id' => 'Comment ID',
				'goods_id' => 'Goods ID',
				'email' => 'Email',
				'username' => 'Username',
				'content' => 'Content',
				'add_time' => 'Add Time',
				'ip_address' => 'Ip Address',
				'is_show' => 'Is Show',
				'parent_id' => 'Parent ID',
				'user_id' => 'User ID',
				'img' => 'Img',
				'order_id' => 'Order ID',
				'deliver_rank' => 'Deliver Rank',
				'goods_rank' => 'Goods Rank',
				'service_rank' => 'Service Rank',
				'zan_num' => 'Zan Num',
				'zan_userid' => 'Zan Userid',
				'is_anonymous' => 'Is Anonymous' 
		];
	}
	public static function findByGoods($goods_id, $offset, $limit) {
		return static::find ()->select ( 'comment_id, goods_id, username, content, add_time, img, user_id, order_id, zan_num' )->where ( 'goods_id=:goods_id ', [ 
				'goods_id' => $goods_id 
		] )->orderBy ( 'add_time' )->offset ( $offset )->limit ( $limit )->all ();
	}
	public static function findgoods($goods_id) {
		return Comment::find ()->where ( 'goods_id=:goods_id ', [ 
				'goods_id' => $goods_id 
		] )->count ();
	}
}
