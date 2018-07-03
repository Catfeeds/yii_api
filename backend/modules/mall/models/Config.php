<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_config".
 *
 * @property integer $id
 * @property string $title
 * @property string $keywords
 * @property integer $logo_id
 * @property string $description
 * @property string $copyright
 * @property string $theme
 * @property string $tongji_code
 * @property string $created_at
 * @property string $updated_at
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'keywords', 'logo_id', 'description', 'copyright', 'theme'], 'required'],
            [['id', 'logo_id'], 'integer'],
            [['title', 'keywords', 'description', 'copyright', 'theme', 'tongji_code'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
            'keywords' => 'Keywords',
            'logo_id' => 'Logo ID',
            'description' => 'Description',
            'copyright' => 'Copyright',
            'theme' => 'Theme',
            'tongji_code' => 'Tongji Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
