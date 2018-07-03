<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_lottery".
 *
 * @property integer $id
 * @property integer $activity_id
 * @property integer $user_id
 * @property integer $mould_id
 * @property string $lottery_title
 * @property string $lottery_content
 * @property string $image
 * @property integer $lottery_ways
 * @property integer $creat_time
 * @property integer $update_time
 * @property integer $end_time
 * @property integer $count
 * @property integer $show_luckyman
 */
class Lottery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lottery}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'user_id', 'mould_id', 'lottery_ways', 'creat_time', 'update_time', 'end_time', 'count', 'show_luckyman'], 'integer'],
            [['user_id', 'lottery_title'], 'required'],
            [['lottery_content'], 'string'],
            [['lottery_title'], 'string', 'max' => 64],
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
            'activity_id' => 'Activity ID',
            'user_id' => 'User ID',
            'mould_id' => 'Mould ID',
            'lottery_title' => 'Lottery Title',
            'lottery_content' => 'Lottery Content',
            'image' => 'Image',
            'lottery_ways' => 'Lottery Ways',
            'creat_time' => 'Creat Time',
            'update_time' => 'Update Time',
            'end_time' => 'End Time',
            'count' => 'Count',
            'show_luckyman' => 'Show Luckyman',
        ];
    }
}
