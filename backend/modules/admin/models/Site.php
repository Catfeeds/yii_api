<?php

namespace backend\modules\admin\models;

use Yii;

/**
 * This is the model class for table "pre_site".
 *
 * @property integer $site_id
 * @property integer $user_id
 * @property integer $site_url
 * @property string $image
 * @property string $logo
 * @property string $name
 * @property string $state
 * @property integer $on_work
 * @property string $description
 * @property string $keywords
 * @property string $site_title
 * @property integer $created_at
 * @property integer $expires
 * @property integer $wxname
 * @property integer $appid
 * @property integer $appsecret
 * @property integer $wxid
 */
class Site extends \yii\db\ActiveRecord
{
    const NOT_VERYIFY = 0;
    const IN_VERYIFY = 1;
    const PASS_VERYIFY = 2;
    const NOT_PASS_VERYIFY = 3;
    
    const ON_WORK = 1;
    const NOT_WORK = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','site_url','name', 'description'], 'required'],
            [['site_id','on_work','user_id', 'created_at', 'expires', 'wxname', 'appid', 'appsecret', 'wxid'], 'integer'],
            [['image','logo'], 'string'],
            [['name', 'state','site_url'], 'string', 'max' => 255],
            [['keywords', 'site_title'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'site_id' => 'site_id',
            'user_id' => 'User ID',
            'site_url' => 'Site Url',
            'image' => 'Image',
            'logo'=>'Logo',
            'name' => 'Name',
            'state' => 'State',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'site_title' => 'Site Title',
            'created_at' => 'Created At',
            'expires' => 'Expires',
            'wxname' => 'Wxname',
            'appid' => 'Appid',
            'appsecret' => 'Appsecret',
            'wxid' => 'Wxid',
        ];
    }
    
    public static function getMySite($user_id){
        return static::find()->select('site_id,logo,name,description,state,created_at,expires')->where('user_id = :user_id',[':user_id'=>$user_id])->all();
    }
    public static function getMySiteone($user_id,$site_id){
        return static::find()->where('user_id = :user_id and site_id = :site_id',[':user_id'=>$user_id,':site_id'=>$site_id])->one();
    }
}
