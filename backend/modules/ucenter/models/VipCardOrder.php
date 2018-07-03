<?php

namespace backend\modules\ucenter\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * 会员卡充值订单
 * Class VipCardOrder
 * @package backend\modules\ucenter\models
 */
class VipCardOrder extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vip_card_order}}';
    }

    public function rules()
    {
        return [
            [['phone', 'weicardnum', 'order_num', 'money'], 'required'],


            [['phone', 'weicardnum', 'order_num', 'money'], 'number'],

        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
            ],
        ];
    }


}
