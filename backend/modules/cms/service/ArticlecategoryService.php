<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\cms\service;

use backend\modules\cms\models\ArticleCategory;
use backend\modules\cms\models\Article;

class ArticlecategoryService
{
    
    public static function showallcategory($site_id){
        return ArticleCategory::getAllCategory($site_id);
    }
    public static function showcategory($site_id)
    {
        return ArticleCategory::getCategoryall($site_id);
    }
    public static function showallcategorywithpid($site_id,$pid=0){
        return ArticleCategory::getCategorywithpid($site_id,$pid);
    }
    public static function showallparent($site_id,$id)
    {
        $array = [];
        $cat = ArticleCategory::findOne(['id'=>$id]);
        array_push($array,$cat);
        while($cat['parentid']!=0)
        {
            $cat = ArticleCategory::findOne(['id'=>$cat['parentid']]);
            array_push($array,$cat);
        }
        return $array;
    }
    //添加分类
    public static function create($data)
    {
        $model = new ArticleCategory();
        $model->created_at = time();
        
        if($model->load($data,'')&&$model->validate()){
            if($model->save()){
                self::thechild($model->parentid);
                return $model;
            }
        }
        return null;
    }
    public static function update($data){
        $model = ArticleCategory::getCategory($data['site_id'],$data['id']);
        if(!empty($model)&&$model->load($data,'')&&$model->validate()){
            $model->updated_at = time();
            if($model->save()){
                self::thechild($model->parentid);
                return $model;        
            }
        }
        return null;   
    }
    
    //删除分类
    public static function delete($site_id,$id)
    {
        $model = ArticleCategory::getCategory($site_id,$id);
        if(empty($model)){
            return false;
        }
        Article::updateAll([
                'cat_id' => Article::DEFAULT_CAT_ID
        ], [
                'cat_id' => $id,
                'site_id' => $site_id
        ]);
        self::thechild($model->parentid);
        return $model->delete();
    }
    public static function thechild($id)
    {
        $models = ArticleCategory::findOne(['parentid'=>$id]);
        if(empty($models)){
            $model = ArticleCategory::findOne(['id'=>$id]);
            if(empty($model)){
                $model->children = '';
                $model->save();
            }
        }else{
            if(!empty($model)){
                $model->children = 1;
                $model->save();
            }
            
        }
    }
}


