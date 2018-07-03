<?php
//Jason

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $catid
 * @property integer $siteid
 * @property string $module
 * @property integer $type
 * @property integer $modelid
 * @property integer $parentid
 * @property string $arrparentid
 * @property integer $child
 * @property string $arrchildid
 * @property string $catname
 * @property string $style
 * @property string $image
 * @property string $description
 * @property string $parentdir
 * @property string $catdir
 * @property string $url
 * @property integer $items
 * @property integer $hits
 * @property string $setting
 * @property integer $listorder
 * @property integer $ismenu
 * @property integer $sethtml
 * @property string $letter
 * @property string $usable_type
 */
class Category extends \yii\db\ActiveRecord
{
//     public $my;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['siteid', 'type', 'modelid', 'parentid', 'child', 'items', 'hits', 'listorder', 'ismenu', 'sethtml'], 'integer'],
            [['module', 'arrparentid', 'arrchildid', 'catname', 'style', 'image', 'description', 'parentdir', 'catdir', 'url', 'setting', 'letter', 'usable_type'], 'required'],
            [['arrchildid', 'description', 'setting'], 'string'],
            [['module'], 'string', 'max' => 15],
            [['arrparentid', 'usable_type'], 'string', 'max' => 255],
            [['catname', 'catdir', 'letter'], 'string', 'max' => 30],
            [['style'], 'string', 'max' => 5],
            [['image', 'parentdir', 'url'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'catid' => 'Catid',
            'siteid' => 'Siteid',
            'module' => 'Module',
            'type' => 'Type',
            'modelid' => 'Modelid',
            'parentid' => 'Parentid',
            'arrparentid' => 'Arrparentid',
            'child' => 'Child',
            'arrchildid' => 'Arrchildid',
            'catname' => 'Catname',
            'style' => 'Style',
            'image' => 'Image',
            'description' => 'Description',
            'parentdir' => 'Parentdir',
            'catdir' => 'Catdir',
            'url' => 'Url',
            'items' => 'Items',
            'hits' => 'Hits',
            'setting' => 'Setting',
            'listorder' => 'Listorder',
            'ismenu' => 'Ismenu',
            'sethtml' => 'Sethtml',
            'letter' => 'Letter',
            'usable_type' => 'Usable Type',
        ];
    }

    //获取子集
    public static function get_child($myid)
    {
        return Category::find()->select('siteid,catid,name')->where(['parentid' => $myid])->all();
    }

}
