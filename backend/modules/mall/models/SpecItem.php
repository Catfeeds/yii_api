<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_spec_item".
 *
 * @property integer $id
 * @property integer $spec_id
 * @property string $item
 */
class SpecItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spec_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['spec_id', 'goods_id','item'], 'required'],
        	[['id', 'spec_id', 'goods_id'], 'integer'],
            [['item'], 'string', 'max' => 54],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spec_id' => 'Spec ID',
        	'goods_id' => 'Goods ID',
            'item' => 'Item',
        ];
    }
    
    public static function findByid($id) {
    	return static::findOne ( [
    			'id' => $id,
    	] );
    }
    
    public static function findBygoodsid($goods_id){
    	return static::findAll( [
    			'goods_id' => $goods_id,
    	] );
    }
}
