<?php

namespace backend\modules\ucenter\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class VipCard extends \yii\db\ActiveRecord
{
    //1储值卡  2折扣  3代金券   4储值卡+折扣
    const STORED_SUM = 1;
    const DISCOUNT = 2;
    const CASH_SUM = 3;
    const STORED_CASH = 4;


    //微信的Card  类型
    const WE_CASH = "CASH";  //代金券
    const WE_DISCOUNT = "DISCOUNT";  //折扣卡
    const WE_Card = "MEMBER_CARD";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vip_card}}';
    }

    public function rules()
    {
        return [
            [['card_type', 'card_name', 'end_time', 'site_id'], 'required'],


            [['stored_sum', 'cash_sum', 'discount', 'end_time', 'site_id'], 'number'],

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

    //通过ID 转成名字
    public function getName($id)
    {

        if ($id == 1) {
            return "储值卡";
        } else if ($id == 2) {
            return "折扣卡";
        } else if ($id == 3) {
            return "代金券";
        } else if ($id == 4) {
            return "储值折扣卡";
        }

    }


    //通过名字转成ＩＤ
    public static function getID($name)
    {

        if ($name == "储值卡") {
            return 1;
        } else if ($name == "折扣卡") {
            return 2;
        } else if ($name == "代金券") {
            return 3;
        } else if ($name == "储值折扣卡") {
            return 4;
        }

    }


}
