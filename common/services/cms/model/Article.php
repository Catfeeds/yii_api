<?php
//Jason
namespace common\services\cms\model;

use Yii;
use common\services\cms\model\ArticleData;
/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
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
            [['cat_id', 'title', 'author'], 'required'],
            [['cat_id', 'user_id', 'click', 'status', 'created_at', 'updated_at', 'praise'], 'integer'],
            [['title', 'sub', 'remark'], 'string'],
            [['author'], 'string', 'max' => 30],
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
        ];
    }

    public function getArticledata()
    {
        /**
         * 第一个参数为要关联的字表模型类名称，
         * 第二个参数指定 通过子表的 customer_id 去关联主表的 id 字段
         */
        return $this->hasOne(ArticleData::className(), ['id' => 'id']);
    }
}
