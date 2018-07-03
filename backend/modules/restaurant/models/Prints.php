<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_prints".
 *
 * @property integer $id
 * @property integer $brand
 * @property integer $site_id
 * @property string $name
 * @property string $eq_number
 * @property string $eq_key
 * @property integer $status
 * @property integer $print_num
 * @property string $print_for
 * @property integer $print_show
 */
class Prints extends \yii\db\ActiveRecord
{
    const WORK_PRINT = 0;
    const NOT_WORK_PRINT =1;
    
    const OUT_PRINT = 1;
    const NOT_OUT_PRINT =2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%prints}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand', 'site_id', 'name', 'eq_number', 'eq_key','print_num'], 'required'],
            [['brand', 'site_id', 'print_num', 'print_show'], 'integer'],
            [['name', 'eq_number', 'eq_key'], 'string', 'max' => 64],
            [['print_for'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand' => 'Brand',
            'site_id' => 'Site ID',
            'name' => 'Name',
            'eq_number' => 'Eq Number',
            'eq_key' => 'Eq Key',
            'status' => 'Status',
            'print_num' => 'Print Num',
            'print_for' => 'Print For',
            'print_show' => 'Print Show',
        ];
    }
    public static function showbysite($site_id)
    {
        return static::findAll(['site_id'=>$site_id]);
    } 
}
