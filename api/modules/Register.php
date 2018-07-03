<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "register".
 *
 * @property integer $id
 * @property integer $create_at
 * @property integer $department
 * @property integer $pet_id
 */
class Register extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'register';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_at', 'department', 'pet_id'], 'required'],
            [['create_at', 'department', 'pet_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_at' => 'Create At',
            'department' => 'Department',
            'pet_id' => 'Pet ID',
        ];
    }
}
