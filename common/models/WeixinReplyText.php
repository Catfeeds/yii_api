<?php
/*
 *  @author Jason
 *  微信文本消息回复
 */
namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%weixin_reply_text}}".
 *
 * @property integer $id
 * @property string $keyword
 * @property integer $keyword_type
 * @property string $content
 * @property integer $view_count
 * @property integer $sort
 * @property string $token
 */
class WeixinReplyText extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_reply_text}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['keyword_type', 'view_count', 'sort'], 'integer'],
            [['content'], 'string'],
            [['keyword', 'token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'keyword' => 'Keyword',
            'keyword_type' => 'Keyword Type',
            'content' => 'Content',
            'view_count' => 'View Count',
            'sort' => 'Sort',
            'token' => 'Token',
        ];
    }
}
