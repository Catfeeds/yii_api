<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%food_car_num}}".
 *
 * @property integer $tableid
 * @property integer $catid
 * @property integer $num
 * @property integer $user_id
 * @property integer $status
 * @property integer $site_id
 */
class FoodCarNum extends \yii\db\ActiveRecord
{
    const STATUS_API = 0;
    const STATUS_H5 = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%food_car_num}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tableid', 'catid'], 'required'],
            [['tableid', 'catid', 'num','user_id','status','site_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tableid' => 'Tableid',
            'catid' => 'Catid',
            'num' => 'Num',
        ];
    }
}
