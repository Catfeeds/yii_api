<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_lottery_prizes_goods".
 *
 * @property integer $id
 * @property integer $prizes_id
 * @property string $goods_name
 * @property string $goods_content
 * @property integer $goods_nums
 */
class LotteryGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lottery_prizes_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prizes_id', 'goods_name', 'goods_nums'], 'required'],
            [['prizes_id', 'goods_nums'], 'integer'],
            [['goods_name'], 'string', 'max' => 64],
            [['goods_content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prizes_id' => 'Prizes ID',
            'goods_name' => 'Goods Name',
            'goods_content' => 'Goods Content',
            'goods_nums' => 'Goods Nums',
        ];
    }
}
