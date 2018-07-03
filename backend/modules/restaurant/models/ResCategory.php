<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_res_category".
 *
 * @property integer $cat_id
 * @property integer $site_id
 * @property string $name
 * @property integer $order_num
 * @property integer $cat_top
 */
class ResCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%res_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'name'], 'required'],
            [['site_id', 'order_num', 'cat_top','category_count'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => 'Cat ID',
            'site_id' => 'Site ID',
            'name' => 'Name',
            'order_num' => 'Order Num',
            'cat_top' => 'Cat Top',
            'category_count'=>'Category count'
        ];
    }
    
    public static function findbysite($siteid){
        $models = static::find()->where('site_id = :site_id',['site_id'=>$siteid])->all();
        return $models;
    }
    public static function findbyid($id){
        return static::findOne(['cat_id'=>$id]);
    }
}
