<?php

namespace backend\modules\wechat\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_material_news}}".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $title
 * @property string $author
 * @property integer $cover_id
 * @property string $intro
 * @property string $content
 * @property string $link
 * @property integer $group_id
 * @property string $thumb_media_id
 * @property string $media_id
 * @property integer $manager_id
 * @property string $token
 * @property integer $created_at
 * @property string $url
 * @property integer $is_use
 * @property integer $aim_id
 * @property string $aim_table
 * @property integer $updated_at
 */
class MaterialNews extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_material_news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id','title','content','thumb_media_id',], 'required'],
            [['id', 'cover_id', 'group_id', 'manager_id', 'created_at', 'is_use', 'aim_id', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title', 'thumb_media_id', 'media_id', 'token'], 'string', 'max' => 100],
            [['author'], 'string', 'max' => 30],
            [['intro', 'link', 'url', 'aim_table'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Author',
            'cover_id' => 'Cover ID',
            'intro' => 'Intro',
            'content' => 'Content',
            'link' => 'Link',
            'group_id' => 'Group ID',
            'thumb_media_id' => 'Thumb Media ID',
            'media_id' => 'Media ID',
            'manager_id' => 'Manager ID',
            'token' => 'Token',
            'created_at' => 'Created At',
            'url' => 'Url',
            'is_use' => 'Is Use',
            'aim_id' => 'Aim ID',
            'aim_table' => 'Aim Table',
            'updated_at' => 'Updated At',
        ];
    }
}
