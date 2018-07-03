<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pre_upvote".
 *
 * @property integer $id
 * @property integer $use_id
 * @property integer $article_id
 * @property integer $state
 * @property integer $type
 */
class Praise extends \yii\db\ActiveRecord
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
            [['use_id', 'article_id', 'state', 'type'], 'required'],
            [['use_id', 'article_id', 'state', 'type'], 'integer'],
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
            'type' => 'Type',
        ];
    }
}
