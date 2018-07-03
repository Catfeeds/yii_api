<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%article_data}}".
 *
 * @property string $id
 * @property string $content
 * @property integer $readpoint
 * @property string $groupids_view
 * @property integer $paginationtype
 * @property integer $maxcharperpage
 * @property string $template
 * @property integer $paytype
 * @property string $relation
 * @property string $voteid
 * @property integer $allow_comment
 * @property string $copyfrom
 * @property string $lon
 * @property string $lat
 * @property string $oldprice
 * @property string $price
 * @property string $place
 * @property integer $recency
 * @property string $image
 */
class ArticleData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'content', 'groupids_view', 'paginationtype', 'maxcharperpage', 'template', 'lon', 'lat', 'oldprice', 'price', 'place', 'image'], 'required'],
            [['id', 'readpoint', 'paginationtype', 'maxcharperpage', 'paytype', 'voteid', 'allow_comment', 'recency'], 'integer'],
            [['content'], 'string'],
            [['lon', 'lat'], 'number'],
            [['groupids_view', 'copyfrom', 'place'], 'string', 'max' => 100],
            [['template'], 'string', 'max' => 30],
            [['relation', 'image'], 'string', 'max' => 255],
            [['oldprice', 'price'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'readpoint' => 'Readpoint',
            'groupids_view' => 'Groupids View',
            'paginationtype' => 'Paginationtype',
            'maxcharperpage' => 'Maxcharperpage',
            'template' => 'Template',
            'paytype' => 'Paytype',
            'relation' => 'Relation',
            'voteid' => 'Voteid',
            'allow_comment' => 'Allow Comment',
            'copyfrom' => 'Copyfrom',
            'lon' => 'Lon',
            'lat' => 'Lat',
            'oldprice' => 'Oldprice',
            'price' => 'Price',
            'place' => 'Place',
            'recency' => 'Recency',
            'image' => 'Image',
        ];
    }
}
