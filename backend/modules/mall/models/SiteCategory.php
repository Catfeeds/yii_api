<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_site_category".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $category_name
 * @property integer $parentid
 * @property integer $sort_order
 * @property integer $is_show
 */
class SiteCategory extends \yii\db\ActiveRecord
{
	const STATIC_IS_SHOW = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'category_name', 'parentid'], 'required'],
        	[['site_id','parentid', 'sort_order', 'is_show'], 'integer'],
            [['category_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'site_id',
            'category_name' => 'Category Name',
            'parentid' => 'Parentid',
            'sort_order' => 'Sort Order',
            'is_show' => 'Is Show',
        ];
    }
    
    public static function findbyid($id){
    	return static::findOne ( ['id' => $id] );
    }
    public static function showmycategory($site_id){
    	return static::find()->where('site_id = :site_id and is_show = :is_show',[':site_id'=>$site_id,'is_show'=>self::STATIC_IS_SHOW])->orderBy('sort_order')->all();
    }
    public static function showmycategoryasarray($site_id){
    	return static::find()->where('site_id = :site_id and is_show = :is_show',[':site_id'=>$site_id,'is_show'=>self::STATIC_IS_SHOW])->orderBy('sort_order')->asArray()->all();
    }
    public static function showmycategorybypid($site_id,$pid){
    	return static::find()->where('site_id = :site_id and parentid = :parentid and is_show = :is_show',[':site_id'=>$site_id,':parentid'=>$pid,'is_show'=>self::STATIC_IS_SHOW])->orderBy('sort_order')->all();
    }
}
