<?php
namespace backend\modules\restaurant\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\PrintsService;

class PrintsController extends BaseController
{
    // 获取店铺内所有打印机
    public function actionIndex ()
    {
        $site_id = $this->getSite();
        if(empty($site_id)){
            return $this->jsonFail([],'参数缺失');
        }
        $prints= PrintsService::showbysite($site_id);
        if (empty($prints)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($prints, '查询成功');
        }
    }

    // 创建新打印机
    public function actionCreate ()
    {
        
        $user_id = $this->getUserId();
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if(!empty($model = PrintsService::create($data,$user_id))){
            if($model['msg']==1){
                return $this->jsonSuccess($model['print'],'创建成功');
            }elseif($model['msg']==2){
                return $this->jsonFail($model['msg'],'重复创建');
            }else{
                return $this->jsonFail($model['msg'],'创建失败,请检查您的输入信息');
            }
        }else{
            return $this->jsonFail([],'创建失败');
        }
    }
    
    // 修改打印机
    public function actionUpdate ()
    {
        $site_id = $this->getSite();
        $user_id = $this->getUserId();
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if(empty($data['id'])){
            return $this->jsonFail([],'参数缺失');
        }
        if(!empty($model = PrintsService::update($data,$user_id,$site_id))){
            if($model['msg']==1){
                return $this->jsonSuccess($model['print'],'修改成功');
            }elseif($model['msg']==2){
                return $this->jsonFail($model['msg'],'打印机已经创建,无法再次创建');
            }elseif($model['msg']==2002){
                return $this->jsonFail($model['msg'],'打印机已经修改失败,已回滚');
            }else{
                return $this->jsonFail($model['msg'],'创建失败,请检查您的输入信息');
            }
        }else{
            return $this->jsonFail([],'修改失败');
        }
    }
    
    // 删除打印机
    public function actionDelete ()
    {
        // 这里要判断是否为本店铺管理员。
        $id= Yii::$app->request->post('id');
        $site_id= $this->getSite();
        if(PrintsService::delete($id,$site_id)){
            return $this->jsonSuccess($id,'删除成功');
        }else{
            return $this->jsonFail([],'删除失败');
        }
    }
}