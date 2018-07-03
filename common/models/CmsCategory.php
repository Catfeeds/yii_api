<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cms_category".
 *
 * @property integer $id
 * @property string $parent_id
 * @property string $parent_ids
 * @property string $site_id
 * @property string $office_id
 * @property string $module
 * @property string $name
 * @property string $image
 * @property string $href
 * @property string $target
 * @property string $description
 * @property string $keywords
 * @property integer $sort
 * @property string $in_menu
 * @property string $in_list
 * @property string $show_modes
 * @property string $allow_comment
 * @property string $custom_list_view
 * @property string $custom_content_view
 * @property string $view_config
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_by
 * @property integer $updated_at
 * @property string $remarks
 * @property integer $status
 *
 * @property CmsArticle[] $cmsArticles
 */
class CmsCategory extends \common\components\ETActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['siteid', 'parentid'], 'required'],
            [['siteid', 'parentid', 'parent_id', 'level', 'sort_order', 'is_show', 'is_hot', 'cat_group', 'commission_rate'], 'integer'],
            [['name'], 'string', 'max' => 90],
            [['mobile_name'], 'string', 'max' => 64],
            [['parent_id_path'], 'string', 'max' => 128],
            [['image'], 'string', 'max' => 512],
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
            'name' => 'Name',
            'parentid' => 'Parentid',
            'mobile_name' => 'Mobile Name',
            'parent_id' => 'Parent ID',
            'parent_id_path' => 'Parent Id Path',
            'level' => 'Level',
            'sort_order' => 'Sort Order',
            'is_show' => 'Is Show',
            'image' => 'Image',
            'is_hot' => 'Is Hot',
            'cat_group' => 'Cat Group',
            'commission_rate' => 'Commission Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticles()
    {
        return $this->hasMany(CmsArticle::className(), ['category_id' => 'id']);
    }
}
