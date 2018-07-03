<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_shipping".
 *
 * @property integer $shipping_id
 * @property string $shipping_code
 * @property string $shipping_name
 * @property string $shipping_desc
 * @property string $insure
 * @property integer $enabled
 */
class Shipping extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shipping}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled'], 'integer'],
            [['shipping_code'], 'string', 'max' => 20],
            [['shipping_name'], 'string', 'max' => 120],
            [['shipping_desc'], 'string', 'max' => 255],
            [['insure'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shipping_id' => 'Shipping ID',
            'shipping_code' => 'Shipping Code',
            'shipping_name' => 'Shipping Name',
            'shipping_desc' => 'Shipping Desc',
            'insure' => 'Insure',
            'enabled' => 'Enabled',
        ];
    }
}
