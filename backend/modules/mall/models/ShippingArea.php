<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_shipping_area".
 *
 * @property integer $shipping_area_id
 * @property string $shipping_area_name
 * @property string $shipping_code
 * @property string $config
 * @property integer $update_time
 * @property integer $is_default
 */
class ShippingArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shipping_area}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['config'], 'required'],
            [['config'], 'string'],
            [['update_time', 'is_default'], 'integer'],
            [['shipping_area_name'], 'string', 'max' => 150],
            [['shipping_code'], 'string', 'max' => 50],
            [['region'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shipping_area_id' => 'Shipping Area ID',
            'shipping_area_name' => 'Shipping Area Name',
            'shipping_code' => 'Shipping Code',
            'config' => 'Config',
            'update_time' => 'Update Time',
            'is_default' => 'Is Default',
            'region'=>'Region',
        ];
    }

}
