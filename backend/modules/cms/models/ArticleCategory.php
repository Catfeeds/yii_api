<?php

namespace backend\modules\cms\models;

use Yii;

/**
 * This is the model class for table "pre_article_category".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $name
 * @property integer $parentid
 * @property integer $sort_order
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $children
 */
class ArticleCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','site_id','parentid'], 'required'],
            [['id','site_id','parentid', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['name','children'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'name' => 'Name',
            'parentid' => 'Parentid',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'children' => 'Child'
        ];
    }
    //展示店铺内所有文章分类
    public static function getAllCategory($site_id){
        return static::findAll(['site_id'=>$site_id]);
    }
    //展示店铺
    public static function getCategorywithpid($site_id,$pid){
        return static::findAll(['site_id'=>$site_id,'parentid'=>$pid]);
    }
    //展示店铺
    public static function getCategoryall($site_id,$pid=0){
        $cats = static::findAll(['site_id'=>$site_id,'parentid'=>$pid]);
        if(!empty($cats)){
            foreach ($cats as $i=>$cat){
                $cats[$i]['children'] = self::getCategoryall($site_id,$cat['id']);
            }
        }
        return $cats;
    }
    //准确的店铺文件分类信息
    public static function getCategory($site_id,$id){
        return static::findOne(['site_id'=>$site_id,'id'=>$id]);
    } 
}
