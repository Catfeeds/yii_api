<?php

namespace backend\modules\ucenter\models;

use Yii;


class WeiCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wei_card}}';
    }

    public function rules()
    {
        return [
            [['brand_name', 'title', 'notice', 'description', 'quantity', 'prerogative', 'name', 'tips', 'url','site_id'], 'required'],

        ];
    }


}
