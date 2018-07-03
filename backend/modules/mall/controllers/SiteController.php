<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Site;

class SiteController extends BaseController
{

    // 显示我的店铺
    public function actionMysite ()
    {
        
        $user_id = $this->getUserId();
        $sites = Site::getMySite($user_id);
        if (empty($sites)) {
            return $this->jsonFail([], '未查询到您的店铺');
        } else {
            return $this->jsonSuccess($sites, '查询成功');
        }
    }

    // 显示我的店铺具体信息
    public function actionMysiteone ()
    {
        
        $user_id = $this->getUserId();
        $site_id = Yii::$app->request->get('site_id');
        $site = Site::getMySiteone($user_id, $site_id);
        if (empty($site)) {
            return $this->jsonFail([], '未查询到您的店铺');
        } else {
            return $this->jsonSuccess($site, '查询成功');
        }
    }

    // 修改我的店铺信息
    public function actionUpdatesite ()
    {
        
        $user_id = $this->getUserId();
        $site_id = Yii::$app->request->post('site_id');
        $site = Site::getMySiteone($user_id, $site_id);
        if (empty($site)) {
            return $this->jsonFail([], '未查询到您的店铺');
        }
        $data = Yii::$app->request->post();
        
        if ($site->load($data, '') && $site->save()) {
            return $this->jsonSuccess($site, '修改成功');
        } else {
            return $this->jsonFail($data, '修改失败');
        }
    }
}
