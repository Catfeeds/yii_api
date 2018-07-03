<?php

namespace backend\modules\admin\models;

use Yii;

/**
 * This is the model class for table "{{%site_config}}".
 *
 * @property integer $site_id
 * @property string $geohash
 * @property string $lon
 * @property string $lat
 * @property string $place
 * @property integer $shipping_distance
 * @property string $offer_price
 * @property string $shipping_price
 * @property integer $times
 */
class SiteConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id'], 'required'],
            [['site_id', 'shipping_distance','times'], 'integer'],
            [['lon', 'lat', 'offer_price', 'shipping_price'], 'number'],
            [['geohash'], 'string', 'max' => 20],
            [['place'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'site_id' => 'Site ID',
            'geohash' => 'Geohash',
            'lon' => 'Lon',
            'lat' => 'Lat',
            'place' => 'Place',
            'shipping_distance' => 'Shipping Distance',
            'offer_price' => 'Offer Price',
            'shipping_price' => 'Shipping Price',
        ];
    }
}
