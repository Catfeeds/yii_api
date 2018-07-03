<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "praise".
 *
 * @property integer $id
 * @property integer $use_id
 * @property integer $article_id
 * @property integer $state
 */
class Upvote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%praise}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'use_id', 'article_id', 'state'], 'required'],
            [['id', 'use_id', 'article_id', 'state'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'use_id' => 'Use ID',
            'article_id' => 'Article ID',
            'state' => 'State',
        ];
    }
}
