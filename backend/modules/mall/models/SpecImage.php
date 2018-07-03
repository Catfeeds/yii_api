<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_spec_image".
 *
 * @property integer $goods_id
 * @property integer $spec_image_id
 * @property string $src
 */
class SpecImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spec_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'spec_image_id'], 'integer'],
            [['src'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'spec_image_id' => 'Spec Image ID',
            'src' => 'Src',
        ];
    }
}
