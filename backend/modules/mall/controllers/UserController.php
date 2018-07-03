<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\modules\mall\models\User;
use backend\base\BaseController;
use yii\data\Pagination;

class UserController extends BaseController
{

    // 显示所有用户
    public function actionIndex ()
    {
        
        $page_info = new Pagination(
                [
                        'totalCount' => User::find()->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage(
                empty($this->getParam('page')) ? '' : $this->getParam('page'));
        $users = User::find()->select('username,nickname,mobile,sex,score')->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($users, $page_info);
    }

    // 通过用户名查询
    public function actionFindbyusername ()
    {
        
        $user_name = $this->getParam('username');
        $page_info = new Pagination(
                [
                        'totalCount' => User::find()->where(
                                'username like "%%' . $user_name . '%%"')->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $page_info->setPage(
                empty($this->getParam('page')) ? '' : $this->getParam('page'));
        $users = User::find()->select('username,nickname,mobile,sex,score')->where('username like "%%' . $user_name . '%%"')
            ->offset($page_info->offset)
            ->limit($page_info->limit)
            ->all();
        
        return $this->jsonSuccessWithPage($users, $page_info);
    }

    // 用户id查看用户具体信息
    public function actionView ()
    {
        
        $user_id = $this->getParam('user_id');
        $user = User::find()->select('username,nickname,mobile,sex,score')->where('id=:id', [
                ':id' => $user_id
        ])->one();
        return $this->jsonSuccess($user, '查询成功');
    }
}
