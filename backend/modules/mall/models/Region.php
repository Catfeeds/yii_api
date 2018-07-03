<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_region2".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $level
 */
class Region extends \yii\db\ActiveRecord
{
	
	const STATUS_PROVINCE= 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
    	return '{{%region}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'level'], 'integer'],
            [['name'], 'string', 'max' => 20],
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
            'parent_id' => 'Parent ID',
            'level' => 'Level',
        ];
    }
    public static function findname($id){
        return static::findOne(['id'=>$id])->name;
    }
    public static function getRegions($id){
        $str = '';
        while ($id != 0){
            $reg = static::find()->select('name,parent_id')->where(['id'=>$id])->one();
            if(empty($reg)){
                break;
            }
            $str = $reg->name ." ".$str;
            $id = $reg->parent_id;
        }
        return $str;
    }
    public static function getRegionsarray($id){
        $str = [];
        $i = 4;
        while ($id != 0){
            $reg = static::find()->select('name,parent_id')->where(['id'=>$id])->one();
            if(empty($reg)){
                break;
            }
            $str[$i] = ['name'=>$reg->name,'id'=>$id];
            $i--;
            $id = $reg->parent_id;
        }
        return $str;
    }
}
