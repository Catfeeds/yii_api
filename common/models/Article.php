<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pre_article".
 *
 * @property string $id
 * @property integer $cat_id
 * @property string $title
 * @property integer $user_id
 * @property string $sub
 * @property string $author
 * @property string $remark
 * @property integer $click
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $praise
 * @property integer $tread
 * @property integer $p_id
 */
class Article extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'title', 'user_id', 'sub', 'author', 'status', 'created_at', 'updated_at', 'praise', 'tread', 'p_id', 'thumb'], 'required'],
            [['cat_id', 'user_id', 'click', 'status', 'created_at', 'updated_at', 'praise', 'tread', 'p_id'], 'integer'],
            [['title', 'sub', 'remark'], 'string'],
            [['author'], 'string', 'max' => 30],
            [['thumb'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cat_id' => 'Cat ID',
            'title' => 'Title',
            'user_id' => 'User ID',
            'sub' => 'Sub',
            'author' => 'Author',
            'remark' => 'Remark',
            'click' => 'Click',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'praise' => 'Praise',
            'tread' => 'Tread',
            'p_id' => 'P ID',
            'thumb' => 'Thumb',
        ];
    }

}
