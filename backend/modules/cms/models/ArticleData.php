<?php

namespace backend\modules\cms\models;

use Yii;

/**
 * This is the model class for table "pre_article_data".
 *
 * @property integer $id
 * @property string $content
 * @property integer $readpoint
 * @property string $template
 * @property string $relation
 * @property integer $voteid
 * @property integer $allow_comment
 */
class ArticleData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'content'], 'required'],
            [['id', 'readpoint', 'voteid', 'allow_comment'], 'integer'],
            [['content'], 'string'],
            [['template'], 'string', 'max' => 30],
            [['relation'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'readpoint' => 'Readpoint',
            'template' => 'Template',
            'relation' => 'Relation',
            'voteid' => 'Voteid',
            'allow_comment' => 'Allow Comment',
        ];
    }
    
    public static function getOne($id){
        return static::findOne(['id'=>$id]);
    }
}
