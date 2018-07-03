<?php

namespace api\modules\v1\models;

use Yii;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\FoodSKU;

/**
 * This is the model class for table "pre_food_car".
 *
 * @property integer $id
 * @property integer $table_id
 * @property integer $user_id
 * @property integer $create_at
 * @property integer $food_id
 * @property string $sku
 * @property string $pro
 * @property integer $cat_id
 * @property integer $number
 * @property integer $status
 * @property integer $site_id
 */
class FoodCar extends \yii\db\ActiveRecord
{
    const STATUS_API = 0;
    const STATUS_H5 = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food_car}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_id','user_id', 'number','cat_id'], 'required'],
            [['table_id', 'create_at','user_id', 'food_id', 'number','cat_id','status','site_id'], 'integer'],
            [['sku', 'pro'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_id' => 'Table ID',
            'create_at' => 'Create At',
            'food_id' => 'Food ID',
            'sku' => 'Sku',
            'pro' => 'Pro',
            'cat_id'=>'Cat ID',
            'number' => 'Number',
        ];
    }
    public function getFood()
    {
        return $this->hasOne(Food::className(),['food_id'=>'food_id']);
    }
    public function getSku()
    {
        return $this->hasOne(FoodSKU::className(), ['id'=>'sku']);
    }
}
