<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pre_report".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $article_id
 * @property string $title
 * @property string $content
 * @property integer $created_at
 * @property string $type
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pre_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'article_id', 'created_at'], 'required'],
            [['uid', 'article_id', 'created_at'], 'integer'],
            [['title', 'content', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'article_id' => 'Article ID',
            'title' => 'Title',
            'content' => 'Content',
            'created_at' => 'Created At',
            'type' => 'Type',
        ];
    }
}
