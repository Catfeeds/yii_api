<?php
namespace super\modules\super\controllers;

use Yii;
use super\base\BaseController;
use super\modules\super\service\SuperService;
use yii\data\Pagination;
use super\modules\super\service\AdminService;

class AdminController extends BaseController
{
    //显示所有用户
    public function actionIndex()
    {
        $page_info = new Pagination([
            'totalCount' => AdminService::countuser(),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $admins= AdminService::showuser($page_info->offset,$page_info->limit);
        if(empty($admin)){
            return $this->jsonSuccessWithPage($admins, $page_info);
        }else{
            return $this->jsonFail('','查询失败');
        }
    }
    //根据用户状态查询
    public function actionShowbysta()
    {
        $status = Yii::$app->request->get('status');
        $page_info = new Pagination([
            'totalCount' => AdminService::countbystatus($status),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $admins= AdminService::showbystatus($status,$page_info->offset,$page_info->limit);
        
        if(empty($admin)){
            return $this->jsonSuccessWithPage($admins, $page_info);
        }else{
            return $this->jsonFail('','查询失败');
        }
    }
    //店铺过期的用户查询
    public function actionExpires()
    {
    	$expires = Yii::$app->request->post('expires');
    	if(empty($expires)){
    		return $this->jsonFail([], '过期条件为空');
    	}
        $page_info = new Pagination([
            'totalCount' => AdminService::countexpires(),
            'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
        ]);
        $page_info->setPage($this->getParam('page') - 1);
        $admins= AdminService::showexpires($page_info->offset,$page_info->limit);
       
        if(empty($admin)){
            return $this->jsonSuccessWithPage($admins, $page_info);
        }else{
            return $this->jsonFail('','查询失败');
        }
    }
    
    //查询过期和不过期的用户
    public function actionExpires2()
    {
    	$expires = Yii::$app->request->post('expires');
   
    	if($expires == 1){
    		$page_info = new Pagination([
    				'totalCount' => AdminService::countexpires(),
    				'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
    		]);
    		$page_info->setPage($this->getParam('page') - 1);
    		$admins= AdminService::showexpires($page_info->offset,$page_info->limit);
    	}
    	if($expires == 2){
    		$page_info = new Pagination([
    				'totalCount' => AdminService::countexpires2(),
    				'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
    		]);
    		$page_info->setPage($this->getParam('page') - 1);
    		$admins= AdminService::showexpires2($page_info->offset,$page_info->limit);
    	}
    	if(empty($admin)){
    		return $this->jsonSuccessWithPage($admins, $page_info);
    	}else{
    		return $this->jsonFail('','查询失败');
    	}
    }

    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        if (! empty(SuperService::findbyusername($data['username']))) {
            return $this->jsonFail('', '用户名存在');
        }
        if (SuperService::create($data)) {
            return $this->jsonSuccess('', '添加成功');
        } else {
            return $this->jsonFail('', '添加失败');
        }
    }

    public function actionUpdate()
    {
        $data = Yii::$app->request->post();
        if (SuperService::create($data)) {
            return $this->jsonSuccess('', '添加成功');
        } else {
            return $this->jsonFail('', '添加失败');
        }
    }
    
    public function actionUpdatenote()
    {
        $data = Yii::$app->request->post();
        if(empty($data['id'])){
            return $this->jsonFail('','参数不完整');
        }
        $super_id = $this->getUserId();
        if(AdminService::updateadminnote($super_id, $data)){
            return $this->jsonSuccess('','修改成功');
        }else{
            return $this->jsonFail('','修改失败');
        }
    } 
}
