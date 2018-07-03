<?php
namespace backend\modules\wechat\service;

use common\extensions\Wechat\WechatMedia;
use backend\modules\wechat\models\MaterialTemporary;
use backend\modules\wechat\models\Material;
use yii\base\View;
use backend\modules\wechat\models\MaterialNews;
use backend\modules\wechat\models\MaterialText;

/**
 * 微信素材管理
 * php有版本限制
 */
class MaterialService
{

    /*
     * 文件类型判断
     */
    public static function checkfile($type, $size)
    {
        $array = [
            'image' => ['png','jpeg','jpg','gif'],
            'voice' => ['amr','mp3'],
            'video' => ['mp4'],
            'thumb' => ['jpg']
        ];
        if (in_array($type, $array['image'])) {
            if ($size < 2 * 1024 * 1024) {
                return 'image';
            }
        } elseif (in_array($type, $array['voice'])) {
            if ($size < 2 * 1024 * 1024) {
                return 'voice';
            }
        } elseif (in_array($type, $array['video'])) {
            if ($size < 10 * 1024 * 1024) {
                return 'video';
            }
        } elseif (in_array($type, $array['thumb'])) {
            if ($size < 64 * 1024) {
                return 'thumb';
            }
        }elseif ($type == 'news'){
            return 'news';
        }else {
            return '格式错误';
        }
    }

    
    /*
     * 永久素材
     */
    public static function uploadForeverMedia($data, $type, $site_id = 2,$is_video = false, $video_info = array())
    {
        //$config = siteconfig::getSite($site_id);
        $media = new WechatMedia($site_id);
        if($type == 'video'){
            $is_video = true;
        }
        $msg = $media->uploadForeverMedia($data, $type,$is_video,$video_info);
        if ($msg) {
            return $msg;
        } else {
            return false;
        }
    }
    /*
     * 永久素材总数
     */
    public static function countForever($site_id,$type=null)
    {
        return Material::find()->where([
            'siteid' => $site_id,
        ])->andFilterWhere(['like','type',$type])->count();
    }
    /*
     * 永久素材列表
     */
    public static function listForever($site_id,$type=null,$offset, $limit)
    {
        return Material::find()->select(['id','title','type','cover_url','media_id','is_use','wechat_url','created_at'])
        ->where(['siteid' => $site_id])
        ->andFilterWhere(['like','type',$type])
        ->orderBy('created_at DESC')
        ->offset($offset)
        ->limit($limit)
        ->all();
    }
    /*
     * 获取素材详细
     */
    public static function viewForever($id,$site_id)
    {
        $model = Material::findOne(['id'=>$id]);
        if(empty($model)){
            return null;
        }
        $cover = explode('.', $model->cover_url);
        $media = new WechatMedia($site_id);
        if($model->type == 'vidio'){
            $msg['video'] = $media->getForeverMedia($model->media_id,true);
            
        }else{
            $msg['data'] = $media->getForeverMedia($model->media_id,false);
        }
        $msg['type']= '';
        if($model->type== 'video'||$model->type== 'voice')
        {
            $msg['type']= 'audio/'.end($cover);
        }else{
            
            $msg['type']= 'image/'.end($cover);
        }
       
        return $msg;
    }
    /*
     * 删除素材
     */
    public static function delForever($id,$site_id)
    {
        $model = Material::findOne(['id'=>$id,'siteid'=>$site_id]);
        if(empty($model)){
            return false;
        }
        $media = new WechatMedia($site_id);
        $msg = $media->delForeverMedia($model->media_id);
        return $model->delete();
    }
    
    /*
     * 图文素材
     */
    public static function uploadNews($data, $site_id = 2)
    {
        //$config = siteconfig::getSite($site_id);
        $media = new WechatMedia($site_id);
        $msg = $media->uploadForeverArticles($data);
        if ($msg) {
            return $msg;
        } else {
            return false;
        }
    }
    /*
     * 图文素材
     */
    public static function updateNews($id,$data, $site_id = 2,$index)
    {
        //$config = siteconfig::getSite($site_id);
        $model = MaterialNews::findOne(['id'=>$id]);
        if(empty($model)){
            return false;
        }
        if(!empty($data['title'])) $model->title = $data['title'];
        if(!empty($data['thumb_media_id'])) $model->title = $data['thumb_media_id'];
        if(!empty($data['author'])) $model->title = $data['author'];
        if(!empty($data['show_cover_pic'])) $model->title = $data['show_cover_pic'];
        if(!empty($data['content'])) $model->title = $data['content'];
        if(!empty($data['content_source_url'])) $model->title = $data['content_source_url'];

        $media = new WechatMedia($site_id);
        $msg = $media->updateForeverArticles($model->media_id,$data,$index);
        if ($msg) {
            $model->updated_at = time();
            $model->save();
            return $msg;
        } else {
            return false;
        }
    }
    /*
     * 永久图文素材总数
     */
    public static function countNews($site_id)
    {
        return MaterialNews::find()->select('count(*) as num')->groupBy('group_id')->count();
    }
    /*
     * 永久图文素材列表
     */
    public static function listNews($site_id,$offset, $limit)
    {
        $array = [];
        $groups = MaterialNews::find()->select('group_id')->groupBy('group_id')->orderBy('group_id DESC')->offset($offset)->limit($limit)->asArray()->all();
        foreach ($groups as $group)
        {
            $array[$group['group_id']] = MaterialNews::find()->select(['id','title','author','content','link','thumb_media_id','group_id','media_id','created_at','updated_at'])
            ->where(['group_id' => $group['group_id']])
            ->asArray()
            ->all();
        }
        return $array;
    }
    /*
     * 获取图文素材详细
     */
    public static function viewNews($id,$site_id)
    {
        return MaterialNews::find()->select(['id','title','author','content','link','thumb_media_id','group_id','media_id','created_at','updated_at'])->where(['id' => $id])->andwhere(['site_id' => $site_id])->one();
    }
    /*
     * 按组查询素材
     */
    public static function groupNews($group_id,$site_id)
    {
        return MaterialNews::find()->select(['id','title','author','content','link','thumb_media_id','group_id','media_id','created_at','updated_at'])->where(['group_id' => $group_id])->andwhere(['site_id' => $site_id])->all();
    }
    /*
     * 删除图文素材
     */
    public static function delNews($id,$site_id)
    {
        $model = MaterialNews::find()->select('media_id,group_id')->where(['id'=>$id,'site_id'=>$site_id])->one();
        if(empty($model)){
            return false;
        }
        $media = new WechatMedia($site_id);
        $msg = $media->delForeverMedia($model->media_id);
        if($msg){
            return MaterialNews::deleteAll(['group_id'=>$model->group_id,'site_id'=>$site_id]);
        }else{
            return null;
        }
        
    }
    /*
     * 文本素材
     */
    public static function createText($site_id,$title,$content)
    {
        $text = new MaterialText();
        $text->site_id = $site_id;
        $text->title = $title;
        $text->content = $content;
        $text->created_at = time();
        $text->updated_at = time();
        return $text->save();
    }
    public static function updateText($id,$site_id,$title,$content)
    {
        $text = MaterialText::findOne(['id'=>$id,'site_id'=>$site_id]);
        if (empty($text)){
            return null;
        }
        $text->title = $title;
        $text->content = $content;
        $text->updated_at = time();
        return $text->save();
    }
    public static function delText($id,$site_id)
    {
        $text = MaterialText::findOne(['id'=>$id,'site_id'=>$site_id]);
        if (empty($text)){
            return null;
        }
        return $text->delete();
    }
    public static function countText($site_id)
    {
        return MaterialText::find()->where(['site_id'=>$site_id])->count();
    }
    public static function listText($site_id, $offset, $limit)
    {
        return MaterialText::find()->select([
            'id',
            'title',
            'content',
            'created_at',
            'updated_at'
        ])
        ->where([
                'site_id' => $site_id
        ])
        ->orderBy('created_at DESC')
        ->offset($offset)
        ->limit($limit)
        ->all();
    }
    public static function viewText($id,$site_id)
    {
        return MaterialText::findOne(['id'=>$id,'site_id'=>$site_id]);
    }
    /*
     * 上传图片
     * 图文时使用，大小小于1m
     */
    
//     /*
//      * 上传临时素材
//      */
//     public static function uploadMedia($data, $type, $site_id = 2)
//     {
//         $config = siteconfig::getSite($site_id);
//         $media = new WechatMedia($config);
//         $msg = $media->uploadMedia($data, $type);
//         if ($msg) {
//             return $msg;
//         } else {
//             return false;
//         }
//     }
    
//     /*
//      * 获取临时素材
//      */
//     public static function getMedia($id, $site_id)
//     {
//         //$config = siteconfig::getSite($site_id);
//         $media = new WechatMedia($site_id);
//         $model = MaterialTemporary::find()->select(['media_id','type','created_at'])->where([
//             'id' => $id
//         ])->one();
//         if(empty($model)){
//             return '未查询到';
//         }
//         if (($model->created_at + 3 * 24 * 60 * 60) < time()) {
//             return '素材已过期';
//         }
//         $msg = $media->getMedia($model->media_id, $model->type);
//         if (empty($msg)) {
//             return false;
//         } else {
//             return [
//                 'type' => $model->type,
//                 'data' => $msg
//             ];
//         }
//     }
    
//     /*
//      * 获取临时素材列表
//      * 计算数量
//      * 分页
//      */
//     public static function countMedialist($site_id)
//     {
//         return MaterialTemporary::find()->where([
//             'site_id' => $site_id
//         ])->count();
//     }
    
//     public static function Medialist($site_id, $offset, $limit)
//     {
//         return MaterialTemporary::find()->select([
//             'type',
//             'cover_url',
//             'media_id',
//             'is_use'
//         ])
//         ->where([
//             'siteid' => $site_id
//         ])
//         ->andWhere('created_at > ' . (time() - 3 * 24 * 60 * 60))
//         ->orderBy('created_at DESC')
//         ->offset($offset)
//         ->limit($limit)
//         ->all();
//     }
}