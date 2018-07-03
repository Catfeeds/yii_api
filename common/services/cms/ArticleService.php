<?php
/**
 * @author Jason
 * @date 2016-08-08
 * 没有用
 */
namespace common\services\cms;
use Yii;
use common\services\cms\model\Article;
use common\services\cms\model\ArticleData;

/**
 * AddressController implements the CRUD actions for Address model.
 */
class ArticleService
{
    //获取文章详情
    public function View()
    {
        print_r(Yii::$app->db);exit;
        $model = new Article();
        $data = $model::find()->where(['id' => 1])->with('articledata')->asArray()->one();
        return $data;
    }
}


