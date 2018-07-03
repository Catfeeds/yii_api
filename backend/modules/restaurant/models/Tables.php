<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "pre_tables".
 *
 * @property integer $table_id
 * @property integer $name
 * @property integer $site_id
 * @property string $price
 * @property integer $chair
 * @property integer $people
 * @property string $QR_code
 * @property integer $status
 */
class Tables extends \yii\db\ActiveRecord
{
    const TABLE_HAVE_PEOPLE = 1;
    const TABLE_NULL_PEOPLE = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tables}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['table_id', 'site_id'], 'required'],
                [['table_id', 'site_id', 'chair','status','people'], 'integer'],
                [['QR_code'], 'string', 'max' => 255],
                [['price'], 'number'],
                [['name'],'string','max'=>64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_id' => 'Table ID',
            'site_id' => 'Site ID',
            'chair' => 'Chair',
            'price' => 'Price',
            'people' => 'People',
            'QR_code' => 'Qr Code',
            'status' => 'Status',
        ];
    }
    
    public static function findbysite($siteid){
        return static::findAll(['site_id'=>$siteid]);
    }
    public static function findbytableid($table_id,$site_id){
        return static::find()->where('table_id = :table_id and site_id = :site_id',[':table_id'=>$table_id,':site_id'=>$site_id])->one();
    }
    
    public static function getThetableid($siteid){
        $table = static::find()->select('table_id')->where('site_id =:site_id',[':site_id'=>$siteid])->orderBy('table_id desc')->one();
        if(empty($table)){
            return 1;
        }
        return $table['table_id']+1;
    }
}
