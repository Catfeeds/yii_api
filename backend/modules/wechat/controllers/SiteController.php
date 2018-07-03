<?php
namespace backend\modules\wechat\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\wechat\service\siteconfig;
use backend\modules\wechat\models\UploadForm;

/**
 * 多站点接入
 */
class SiteController extends BaseController
{
    
    public function actionMysiteconfig()
    {
        $site_id = $this->getSite();
        $models = siteconfig::getSite($site_id);
        if(empty($models)){
            return $this->jsonFail([],'未设置');
        }else{
            return $this->jsonSuccess($models,'查询成功');
        }
    }
    public function actionCreate()
    {
        $site_id = $this->getSite();
        $type = Yii::$app->request->post('type');
        if(siteconfig::getCount($site_id, $type) > 0){
            return $this->jsonFail([],'已有无法重复创建');
        }
        $appid = Yii::$app->request->post('appid');
        $appsecret = Yii::$app->request->post('appsecret');
        $mch_id = Yii::$app->request->post('mch_id');
        $partnerkey = Yii::$app->request->post('partnerkey');
        $ssl_cer ='';
        $ssl_key ='';
        if(siteconfig::Create($site_id, $type, $appid, $appsecret, $mch_id, $partnerkey, $ssl_cer, $ssl_key)){
            return $this->jsonSuccess('','创建成功');
        }else{
            return $this->jsonFail('','创建失败');
        }
    }
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $site_id = $this->getSite();
        $type = Yii::$app->request->post('type');
        $appid = Yii::$app->request->post('appid');
        $appsecret = Yii::$app->request->post('appsecret');
        $mch_id = Yii::$app->request->post('mch_id');
        $partnerkey = Yii::$app->request->post('partnerkey');
        $ssl_cer ='';
        $ssl_key ='';
        if(siteconfig::Update($id, $type, $appid, $appsecret, $mch_id, $partnerkey, $ssl_cer, $ssl_key)){
            return $this->jsonSuccess('','修改成功');
        }else{
            return $this->jsonFail('','修改失败');
        }
    }
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $site_id = $this->getSite();
        if(siteconfig::Delete($id, $site_id)){
            return $this->jsonSuccess('','删除成功');
        }else{
            return $this->jsonFail('','删除失败');
        }
    }
    public function actionUploadssl()
    {
        $names = ["apiclient_cert.pem","apiclient_key.pem"];
        $site = $this->getSite();
        $model = new UploadForm();
        $model->file = UploadForm::getInstanceByName ( 'file' );
        $filename = $model->file->name;
        if(in_array($filename, $names)){
            if (! empty ( $model->file ) && $model->validate ()) {
                $url = Yii::$app->params ['upload'].'/'.$site.'/';
                UploadForm::fileExists ( $url);
                if ($model->file->saveAs ( $url. '/' . $filename)) {
                } else {
                    return $this->jsonFail ( $model->file->error, '上传失败' );
                }
            } else {
                return $this->jsonFail ( $model->file, '上传失败,文件格式错误' );
            }
        }else{
            return $this->jsonFail('','请上传正确的文件');
        }
        
    }
}
