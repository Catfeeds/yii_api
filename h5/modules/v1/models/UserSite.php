<?php

namespace h5\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%user_site}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $site_id
 * @property string $money
 * @property string $box_price
 */
class UserSite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_site}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'site_id'], 'required'],
            [['user_id', 'site_id'], 'integer'],
            [['money','box_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'site_id' => 'Site ID',
            'money' => 'Money',
        ];
    }
}
