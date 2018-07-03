<?php

namespace backend\modules\restaurant\models;

use Yii;

/**
 * This is the model class for table "{{%print_template}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $template
 * @property string $image
 * @property integer $brand
 * @property integer $model
 */
class PrintTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%print_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'template'], 'required'],
            [['template'], 'string'],
            [['brand','model'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'template' => 'Template',
            'image' => 'Image',
        ];
    }
}
