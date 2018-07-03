<?php

namespace backend\modules\ucenter\models;

use Yii;


class CardUser extends \yii\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weiuser_vipcard}}';
    }

    public function rules()
    {
        return [
            [['phone', 'weicardnum', 'vipcardid', 'site_id'], 'required'],


            [['phone', 'weicardnum', 'vipcardid', 'site_id'], 'number'],

        ];
    }

    public function getCard()
    {
        return $this->hasOne(VipCard::className(), ['id' => 'vipcardid']);
    }


}
