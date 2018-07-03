<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "{{%sales_days}}".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $sales
 * @property string $days
 * @property integer $order_count
 * @property integer $update_at
 */
class SalesDays extends \yii\db\ActiveRecord
{
    public $sumMoney;
    public $sumCount;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_days}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'days'], 'required'],
            [['site_id', 'update_at','order_count'], 'integer'],
            [['sales'], 'number'],
            [['days'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'sales' => 'Sales',
            'days' => 'Days',
            'update_at' => 'Update At',
        ];
    }
}
