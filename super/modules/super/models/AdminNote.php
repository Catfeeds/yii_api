<?php

namespace super\modules\super\models;

use Yii;

/**
 * This is the model class for table "pre_admin_note".
 *
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property string $wx
 * @property string $qq
 * @property string $note
 * @property integer $super_id
 * @property integer $update_at
 */
class AdminNote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pre_admin_note';
//     	return  '{{%admin_note}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'super_id', 'update_at'], 'integer'],
            [['note'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['mobile', 'qq'], 'string', 'max' => 11],
            [['wx'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'wx' => 'Wx',
            'qq' => 'Qq',
            'note' => 'Note',
            'super_id' => 'Super ID',
            'update_at' => 'Update At',
        ];
    }
    
    public function findbyid(){
    	return AdminNote::find ()->select ( 'id , name , mobile , wx , qq , note' )->where ( [
    			'id' => $id
    	] )->one ();
    }
}
