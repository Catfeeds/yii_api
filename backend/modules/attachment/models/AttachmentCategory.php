<?php

namespace backend\modules\attachment\models;

use Yii;

/**
 * This is the model class for table "pre_attachment_category".
 *
 * @property integer $id
 * @property integer $catid
 * @property integer $site_id
 * @property string $name
 * @property integer $last_update
 */
class AttachmentCategory extends \yii\db\ActiveRecord
{
    const DEFAULT_ID = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachment_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catid', 'site_id', 'name'], 'required'],
            [['catid', 'site_id', 'last_update'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'catid' => 'Catid',
            'site_id' => 'site_id',
            'name' => 'Name',
            'last_update' => 'Last Update',
        ];
    }
    
    //展示店铺内所有文件分类
    public static function getAllCategory($site_id){
        return static::findAll(['site_id'=>$site_id]);
    }
    //准确的店铺文件分类信息
    public static function getCategory($site_id,$catid){
        return static::findOne(['site_id'=>$site_id,'catid'=>$catid]);
    }
    //创建分类时创建分类id
    //如果没有分类 则创建一个默认分类
    public static function getTheCatid($site_id){
        $catid = static::find()->select('catid')->where('site_id =:site_id',[':site_id'=>$site_id])->orderBy('catid desc')->one();
        if(empty($catid)){
            $cateogyr = new AttachmentCategory();
            $cateogyr->site_id = $site_id;
            $cateogyr->catid = 0;
            $cateogyr->name = '默认分类';
            $cateogyr->last_update = time();
            $cateogyr->save();
            
            return 1;
        }
        return $catid['catid']+1;
    }
    
}
