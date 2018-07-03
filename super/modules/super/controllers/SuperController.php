<?php
namespace super\modules\super\controllers;

use Yii;
use super\base\BaseController;
use super\modules\super\service\SuperService;
use yii\data\Pagination;

class SuperController extends BaseController
{

    public function actionLogin()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');
        $access_token = SuperService::login($username, $password);
        if (empty($access_token)) {
            return $this->jsonFail('', '登陆失败');
        } else {
            return $this->jsonSuccess([
                'access_token' => $access_token,
            ],'登陆成功');
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
        if (SuperService::update($data)) {
            return $this->jsonSuccess('', '修改成功');
        } else {
            return $this->jsonFail('', '修改失败');
        }
    }
    
    public function actionIndex(){
    	$page_info = new Pagination([
    			'totalCount' => SuperService::showsupercount(),
    			'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam('per-page')
    	]);
    	$page_info->setPage($this->getParam('page') - 1);
    	$admins= SuperService::showsuper($page_info->offset,$page_info->limit);
    	if(empty($admin)){
    		return $this->jsonSuccessWithPage($admins, $page_info);
    	}else{
    		return $this->jsonFail('','查询失败');
    	}
    }
    
    public function actionView() {
    	$id = Yii::$app->request->post ( "id" );
    	if (empty ( $id )) {
    		return $this->jsonFail ( [ ], '未获取到id' );
    	}
    	$admin = SuperService::findbyid ( $id );
    	if (empty ( $admin )) {
    		return $this->jsonFail ( [ ], '未查询到' );
    	}
    	return $this->jsonSuccess ( $admin, '查询成功' );
    }
}
