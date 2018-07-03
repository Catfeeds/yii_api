<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_lottery_people".
 *
 * @property integer $id
 * @property integer $lottery_id
 * @property string $name
 * @property integer $sex
 * @property integer $phone
 */
class LotteryPeople extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lottery_people}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'lottery_id', 'name'], 'required'],
            [['id', 'lottery_id', 'sex', 'phone'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lottery_id' => 'Lottery ID',
            'name' => 'Name',
            'sex' => 'Sex',
            'phone' => 'Phone',
        ];
    }
}
