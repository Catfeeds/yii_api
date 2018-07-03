<?php

namespace backend\modules\rabc\models;

use Yii;

class UserRole extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_user_role}}';
    }



}
