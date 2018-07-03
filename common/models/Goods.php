<?php
// @author Jason

namespace common\models;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\data\Pagination;
/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property integer $catid
 * @property string $name
 * @property string $description
 * @property integer $price
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catid', 'price'], 'integer'],
            [['description', 'price' ], 'required'],
            [['description', ], 'string'],
            [['name'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'catid' => 'Catid',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
        ];
    }

    /**
     * @获取商品的详情
     * */
    public static function getItem()
    {
        $id = Yii::$app->request->get('id');
        $query  = new Query();
        $item=$query->select('p.goods_id, p.goods_name, pd.content')
        ->from('{{%goods}} as p')
        ->join('LEFT JOIN','{{%goods_data}} as pd','p.goods_id = pd.id')
        ->where("p.goods_id = $id")
        ->one();
        return $item;
    }

    //获取列表数据
    public static function getList()
    {
        $catid = Yii::$app->request->get('catid');
        $query = new Goods();
        $query = $query->find();
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => '10',
        ]);
        $list = $query->orderBy(['goods_id' => SORT_DESC,])->offset($pagination->offset)->limit($pagination->limit)->all();
        return $list;
    }
}
