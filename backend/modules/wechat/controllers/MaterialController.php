<?php
namespace backend\modules\wechat\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\wechat\models\UploadForm;
use yii\web\UploadedFile;
use backend\modules\wechat\service\MaterialService;
use backend\modules\wechat\models\MaterialTemporary;
use yii\data\Pagination;
use backend\modules\wechat\models\Material;
use backend\modules\wechat\models\MaterialNews;

/**
 * 微信素材管理
 */
class MaterialController extends BaseController
{
    /*
     * 上传永久素材
     */
    public function actionForevermedia()
    {
        set_time_limit(0);
        $info = null;
        $is_video = false;
        
        $site_id = $this->getSite();
        $file = UploadedFile::getInstanceByName ( 'file' );
        //上传到微信服务器。
        $filename = $file->name;
        $extension = $file->extension;
        $size = $file->size;
        $type = MaterialService::checkfile($extension, $size);
        if($type== '格式错误'){
            return $this->jsonFail($type);
        }
        $video_title = Yii::$app->request->post('title');
        $video_introduction= Yii::$app->request->post('introduction');
        if($type == 'video'&&(empty($video_title)||empty($video_introduction))){
            return $this->jsonFail('视频上传需标题和简介');
        }
        $name = Yii::$app->security->generateRandomString () . "." . $extension;
        $url = 'upload/' . date ( 'Y' ) . '/' . date ( 'md' );
        $urls = Yii::$app->params ['upload'] . $url;
        UploadForm::fileExists ( $urls );
        if (!$file->saveAs ( $urls . '/' . $name )) {
            return $this->jsonFail ( $file->error, '上传失败' );
        }else{
            $is_video = true;
            $info = ['title'=>$video_title,'introduction'=>$video_introduction];
        }
        $data = ['media'=>'@'.$urls . '/' . $name];
        //素材表保存
        $msgs = MaterialService::uploadForeverMedia($data,$type,$site_id,$is_video,$info);
//         if($type == 'video'||$type == 'voice')
//         {
//             $type = 'audio';
//         }
        if(!empty($msgs)){
            
             $temporary = new Material();
             $temporary->title = $filename;
             $temporary->siteid = $site_id;
             $temporary->cover_url = $url . '/' . $name;
             $temporary->media_id = $msgs['media_id'];
             $temporary->created_at = time();
             $temporary->type = $type;
             if(!empty($msgs['url'])){
                 $temporary->wechat_url = $msgs['url'];
             }
             $temporary->save();
            
            return $this->jsonSuccess($msgs,'上传成功');
        }else{
            return $this->jsonFail ( $file->error, '上传失败' );
        }
    }
    
    /*
     * 获取素材列表
     */
    public function actionListforevermedia()
    {
        $site_id = $this->getSite();
        $type = Yii::$app->request->get('type');
        $page_info = new Pagination([
            'totalCount' => MaterialService::countForever($site_id,$type),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $offset = $page_info->offset;
        $limit = $page_info->limit;
        $list = MaterialService::listForever($site_id,$type, $offset, $limit);
        if (empty($list)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccessWithPage($list, $page_info);
        }
    }
        
    /*
     * 获取素材详细
     */
    public function actionViewforever()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->get('id');
        $msg = MaterialService::viewForever($id, $site_id);
        if(!empty($msg['video'])){
            print_r($msg['video']);
        }elseif(!empty($msg['data'])){
            header('Cache-control: max-age=3600');
            header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600) . ' GMT');
            header('Content-Type: ' . $msg['type']);
            header('Content-Transfer-Encoding: binary');
            echo $msg['data'];
        }else{
            return $this->jsonFail ();
        }
    }
    
    /*
     * 删除素材
     */
    public function actionDelforever()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->post('id');
        $msg = MaterialService::delForever($id, $site_id);
        if(!empty($msg)){
            return $this->jsonSuccess('','删除成功');
        }else{
            return $this->jsonFail ('','删除失败');
        }
    }
    
    /*
     * 图文消息
     */
    public function actionCreatenews()
    {
        $site_id = $this->getSite();
        $datas = Yii::$app->request->post('news');
        $arrays = [];
        foreach ($datas as $data){
            $array = [
                "title"=>$data['title'],
                "thumb_media_id"=>$data['thumb_media_id'],
                "author"=>$data['author'],
                "show_cover_pic"=>$data['show_cover_pic'],
                "content"=>$data['content'],
                "content_source_url"=>$data['content_source_url']
            ];
            array_push($arrays,$array);
        }
        $jsonArr = array(
            "articles"=> $arrays
        );
        
        $msgs = MaterialService::uploadNews($jsonArr,$site_id);
        if(!empty($msgs)){
            $group_id= 0;
            foreach ($datas as $data){
                $cover_id = Material::find()->select('id')->where(['media_id'=>$data['thumb_media_id']])->one();
                $news = new MaterialNews();
                $news->author = $data['author'];
                $news->site_id = $site_id;
                $news->cover_id = $cover_id['id'];
                $news->title = $data['title'];
                $news->thumb_media_id= $data['thumb_media_id'];
                $news->content = $data['content'];
                $news->link = $data['content_source_url'];
                $news->media_id = $msgs['media_id'];
                $news->created_at = time();
                $news->updated_at = time();
                if($group_id!= 0){
                    $news->group_id = $group_id;
                }
                $news->save();
                if($group_id== 0){
                    $group_id= $news->id;
                    $news->group_id = $group_id;
                    $news->save();
                }
            }
            
            return $this->jsonSuccess($msgs,'上传成功');
        }else{
            return $this->jsonFail ( '', '上传失败' );
        }
    }
    /*
     * 修改素材
     * 图文消息
     */
    public function actionUpdatenews()
    {
        $site_id = $this->getSite();
        $data = Yii::$app->request->post();
        if(empty($data['index'])){
            $index = 0;
        }
        if(empty($data['id'])){
            return $this->jsonFail ( '', '修改失败' );
        }
        $array = ["articles"=> [
            "title"=>$data['title'],
            "thumb_media_id"=>$data['thumb_media_id'],
            "author"=>$data['author'],
            "show_cover_pic"=>$data['show_cover_pic'],
            "content"=>$data['content'],
            "content_source_url"=>$data['content_source_url']
        ]];
        $msgs = MaterialService::updateNews($data['id'],$array,$site_id,$index);
        if(!empty($msgs)){
            $news = MaterialNews::findOne(['id'=>$data['id']]);
            $news->author = $data['author'];
            $news->site_id = $site_id;
            $news->title = $data['title'];
            $news->thumb_media_id= $data['thumb_media_id'];
            $news->content = $data['content'];
            $news->link = $data['content_source_url'];
            $news->updated_at = time();
            
            $news->save();
            return $this->jsonSuccess($msgs,'修改成功');
        }else{
            return $this->jsonFail ( '', '修改失败' );
        }
    }
    /*
     * 获取素材列表
     */
    public function actionListnews()
    {
        $site_id = $this->getSite();
        $page_info = new Pagination([
            'totalCount' => MaterialService::countNews($site_id),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $offset = $page_info->offset;
        $limit = $page_info->limit;
        $list = MaterialService::listNews($site_id, $offset, $limit);
        if (empty($list)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccessWithPage($list, $page_info);
        }
    }
    public function actionGroupnews()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->get('group_id');
        $msg = MaterialService::groupNews($id, $site_id);
        if(!empty($msg)){
            return $this->jsonSuccess($msg);
        }else{
            return $this->jsonFail ();
        }
    }
    /*
     * 获取素材详细
     */
    public function actionViewnews()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->get('id');
        $msg = MaterialService::viewNews($id, $site_id);
        if(!empty($msg)){
            return $this->jsonSuccess($msg);
        }else{
            return $this->jsonFail ();
        }
    }
    /*
     * 删除图文素材
     */
    public function actionDelnews()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->post('id');
        $msg = MaterialService::delNews($id, $site_id);
        if(!empty($msg)){
            return $this->jsonSuccess('','删除成功');
        }else{
            return $this->jsonFail ('','删除失败');
        }
    }
    /*
     * 文本素材
     */
    public function actionCreatetext()
    {
        $site_id = $this->getSite();
        $title = Yii::$app->request->post('title');
        $content = Yii::$app->request->post('content');
        if(empty($title)||empty($content)){
            return $this->jsonFail([],'参数不完整');
        }
        if(MaterialService::createText($site_id, $title, $content)){
            return $this->jsonSuccess([],'创建成功');
        }else {
            return $this->jsonFail([],'创建失败');
        }
    }
    
    public function actionUpdatetext()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->post('id');
        $title = Yii::$app->request->post('title');
        $content = Yii::$app->request->post('content');
        if(empty($title)||empty($content)){
            return $this->jsonFail([],'参数不完整');
        }
        if(MaterialService::updateText($id,$site_id, $title, $content)){
            return $this->jsonSuccess([],'修改成功');
        }else {
            return $this->jsonFail([],'修改失败');
        }
    }
    
    public function actionDeltext()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->post('id');
        
        if(MaterialService::delText($id, $site_id)){
            return $this->jsonSuccess([],'删除成功');
        }else{
            return $this->jsonFail([],'删除失败');
        }
    }
    
    public function actionListtext()
    {
        $site_id = $this->getSite();
        $page_info = new Pagination([
            'totalCount' => MaterialService::countText($site_id),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $offset = $page_info->offset;
        $limit = $page_info->limit;
        $list = MaterialService::listText($site_id, $offset, $limit);
        if (empty($list)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccessWithPage($list, $page_info);
        }
    }
    public function actionViewtext()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->get('id');
        $text = MaterialService::viewText($id, $site_id);
        if(!empty($text)){
            return $this->jsonSuccess($text,'查询成功');
        }else{
            return $this->jsonFail('','查询失败');
        }
    }
    
    /*
     * 上传图片
     * 图文时使用，大小小于1m
     */
    
    /*
//     /*
//      * 上传临时素材
//      */
//     public function actionUploadmedia()
//     {
//         $site_id = $this->getSite();
//         $file = UploadedFile::getInstanceByName ( 'file' );
//         //上传到微信服务器。
//         $filename = $file->name;
//         $extension = $file->extension;
//         $size = $file->size;
//         $type = MaterialService::checkfile($extension, $size);
//         if($type== '格式错误'){
//             return $this->jsonFail($type);
//         }
        
//         $name = Yii::$app->security->generateRandomString () . "." . $extension;
//         $url = 'upload/' . date ( 'Y' ) . '/' . date ( 'md' );
//         $urls = Yii::$app->params ['upload'] . $url;
//         UploadForm::fileExists ( $urls );
//         if (!$file->saveAs ( $urls . '/' . $name )) {
//             return $this->jsonFail ( $file->error, '上传失败' );
//         }
//         $data = ['media'=>'@'.$urls . '/' . $name];
//         //临时素材表保存
//         $msgs = MaterialService::uploadMedia($data,$type,$site_id);
        
//         if(!empty($msgs)){
//             $temporary = new MaterialTemporary();
//             $temporary->siteid = $site_id;
//             $temporary->cover_url = $url . '/' . $name;
//             $temporary->media_id = $msgs['media_id'];
//             $temporary->created_at = time();
//             $temporary->type = $type.'/'.$extension;
//             $temporary->save();
//             return $this->jsonSuccess($msgs,'上传成功');
//         }else{
//             return $this->jsonFail ( $file->error, '上传失败' );
//         }
//     }
    
//     /*
//      * 获取临时素材
//      */
//     public function actionGetmedia()
//     {
//         $site_id = $this->getSite();
//         $id = Yii::$app->request->get('id');
//         $msg = MaterialService::getMedia($id, $site_id);
//         if(!empty($msg)){
//             header('Cache-control: max-age=3600');
//             header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600) . ' GMT');
//             header('Content-Type: ' . $msg['type']);
//             header('Content-Transfer-Encoding: binary');
//             echo $msg['data'];
//         }else{
//             return $this->jsonFail ();
//         }
//     }
    
//     /*
//      * 获取临时素材库
//      */
//     public function actionGetmedialist()
//     {
//         $site_id = $this->getSite();
//         $page_info = new Pagination([
//             'totalCount' => MaterialService::countMedialist($site_id),
//             'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
//         ]);
//         $page_info->setPage($this->getParam('page') - 1);
//         $offset = $page_info->offset;
//         $limit = $page_info->limit;
//         $list = MaterialService::Medialist($site_id, $offset, $limit);
//         if (empty($orders)) {
//             return $this->jsonFail([], '未查询到');
//         } else {
//             return $this->jsonSuccessWithPage($list, $page_info);
//         }
//     }
//     */
    
}