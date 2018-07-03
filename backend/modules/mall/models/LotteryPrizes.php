<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_lottery_prizes".
 *
 * @property integer $id
 * @property integer $lottery_id
 * @property string $prizes_title
 * @property string $show_probability
 * @property string $image
 */
class LotteryPrizes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lottery_prizes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lottery_id', 'prizes_title', 'show_probability'], 'required'],
            [['lottery_id'], 'integer'],
            [['prizes_title'], 'string', 'max' => 32],
            [['show_probability'], 'string', 'max' => 64],
            [['image'], 'string', 'max' => 255],
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
            'prizes_title' => 'Prizes Title',
            'show_probability' => 'Show Probability',
            'image' => 'Image',
        ];
    }
}
