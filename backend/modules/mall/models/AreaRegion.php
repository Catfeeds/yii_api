<?php

namespace backend\modules\mall\models;

use Yii;

/**
 * This is the model class for table "pre_area_region".
 *
 * @property string $shipping_area_id
 * @property string $region_id
 */
class AreaRegion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%area_region}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shipping_area_id', 'region_id'], 'required'],
            [['shipping_area_id', 'region_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shipping_area_id' => 'Shipping Area ID',
            'region_id' => 'Region ID',
        ];
    }
    //根据shipping area id 查询 返回id和名称
    public static function findbyshipid($shipping_area_id){
        $areas = static::findAll(['shipping_area_id'=>$shipping_area_id]);
        $thearea = array();
        foreach ($areas as $area){
            $thearea[] =[
                    'region_id'=>$area['region_id'],
                    'name'=>Region::findname($area['region_id'])
            ];
        }
        return $thearea;
    }
}
