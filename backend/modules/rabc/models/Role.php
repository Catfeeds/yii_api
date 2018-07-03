<?php

namespace backend\modules\rabc\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pre_order_food".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $name
 * @property integer $order_id
 * @property integer $food_id
 * @property integer $num
 * @property string $sku_name
 * @property string $pro_name
 * @property integer $finish_num
 * @property integer $sku_id
 * @property integer $property_id
 * @property string $price
 * @property string $box_price
 * @property string $note
 */
class Role extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_role}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
            ],
        ];
    }



}
