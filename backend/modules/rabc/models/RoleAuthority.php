<?php

namespace backend\modules\rabc\models;

use Yii;

class RoleAuthority extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_role_authority}}';
    }



}
