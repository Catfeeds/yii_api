<?php
namespace backend\modules\wechat\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\wechat\service\MessageService;
use yii\data\Pagination;

/**
 * 微信群发消息管理
 */
class MessageController extends BaseController
{
    public function actionCreate()
    {
        $site_id = $this->getSite();
        $type = Yii::$app->request->post('type');
        if(!MessageService::checkType($type)){
            return $this->jsonFail('','格式错误');
        }
        $media_id = Yii::$app->request->post('media_id');
        $content = Yii::$app->request->post('content');
        if(MessageService::Create($site_id,$type,$media_id,$content)){
            return $this->jsonSuccess('','创建成功');
        }else{
            return $this->jsonFail('','创建失败');
        }
    }
    
    public function actionIndex()
    {
        $site_id = $this->getSite();
        $type = Yii::$app->request->get('type');
        $page_info = new Pagination([
            'totalCount' => MessageService::count($site_id,$type),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $offset = $page_info->offset;
        $limit = $page_info->limit;
        $list = MessageService::list($site_id,$type, $offset, $limit);
        if (empty($list)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccessWithPage($list, $page_info);
        }
    }
    
    public function actionView()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->get('id');
        $model = MessageService::view($site_id, $id);
        if (empty($model)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($model,'查询成功');
        }
    }
    
    public function actionSend()
    {
        $site_id = $this->getSite();
        if($site_id != 73){
            return '暂时只允许site_id 73 测试群发消息！';
        }
        $media_id = Yii::$app->request->post('id');
        if(empty($media_id)){
            return $this->jsonFail('参数不完整');
        }
        $msg = MessageService::Send($site_id, $media_id);
        if($msg == 'SUCCESS'){
            return $this->jsonSuccess('','发送成功'); 
        }else{
            return $this->jsonFail('','发送失败');
        }
    }
}