<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\attachment\service;

use Yii;
use backend\modules\attachment\models\Attachment;
use backend\modules\attachment\models\UploadImage;

class UploadService
{
    public static function uploadforueditor($file)
    {            
        $user_id = 0;
        $model = new UploadImage();
        $model->file = $file;
        $filename = $model->file->name;
        $name = Yii::$app->security->generateRandomString() . "." .$model->file->extension;
        if (! empty($model->file) && $model->validate()) {
            $url = 'upload/' . date('Y') . '/' . date('md');
            $urls = Yii::$app->params['upload'] . $url;
            UploadImage::fileExists($urls);
            if ($model->file->saveAs($urls . '/' . $name)) {} else {
                return $this->jsonFail($model->file->error, '上传失败');
            }
        } else {
            return $this->jsonFail($model->file, '上传失败,文件格式错误');
        }
        
        $attachement = new Attachment();
        $attachement->module = Attachment::STATUS_MODULE;
        $attachement->filename = $filename;
        $attachement->filepath = $url . '/' . $name;
        $attachement->filesize = $model->file->size;
        $attachement->fileext = $model->file->type;
        $attachement->isimage = Attachment::STATUS_IS_IMAGE;
        $attachement->uploadtime = time();
        $attachement->uploadip = Yii::$app->request->userIP;
        $attachement->authcode = md5($url);
        $attachement->status = Attachment::STATUS_IS_SHOW;
        
        if ($attachement->save()) {
            $res = array(
                    "state"    => "SUCCESS",          //上传状态，上传成功时必须返回"SUCCESS"
                    "url"      => "$attachement->filepath",
                    "title"    => "$filename",          //新文件名
                    "original" => "$filename",       //原始文件名
                    "type"     => "$attachement->fileext",           //文件类型
                    "size"     => "$attachement->filesize",           //文件大小
            );
            return json_encode($res);
        } else {
            return $this->jsonFail($attachement->errors, '上传最终失败');
        }
    }
}


