<?php
namespace backend\modules\wechat\controllers;

use Yii;
use backend\base\BaseController;
use backend\modules\wechat\service\MenuService;

/**
 * 微信菜单管理
 */
class MenuController extends BaseController
{

    public function actionCreate()
    {
        $site_id = $this->getSite();
        $data = array(
            'name' => Yii::$app->request->post('name'),
            'parentid' => Yii::$app->request->post('parentid'),
            'type' => Yii::$app->request->post('type')
        );
        if ($data['type'] == 'view') {
            $data['url'] = Yii::$app->request->post('url');
            if (empty($data['url'])) {
                return $this->jsonFail([
                    '参数不完整'
                ]);
            }
        } elseif ($data['type'] == 'media_id' || $data['type'] == 'view_limited') {
            $data['media_id'] == Yii::$app->request->post('media_id');
            if (empty($data['media_id'])) {
                return $this->jsonFail([
                    '参数不完整'
                ]);
            }
        } elseif ($data['type'] == 'no') {} else {
            return $this->jsonFail('暂不支持其他菜单选择', '创建失败');
        }
        
        $msg = MenuService::Create($site_id, $data, $data['parentid']);
        if ($msg == 'SUCCESS') {
            return $this->jsonSuccess('', '创建成功');
        } else {
            return $this->jsonFail($msg, '创建失败');
        }
    }

    public function actionUpdate()
    {
        $site_id = $this->getSite();
        $data = array(
            'id' => Yii::$app->request->post('id'),
            'name' => Yii::$app->request->post('name'),
            'parentid' => Yii::$app->request->post('parentid'),
            'type' => Yii::$app->request->post('type')
        );
        if ($data['type'] == 'view') {
            $data['url'] = Yii::$app->request->post('url');
            if (empty($data['url'])) {
                return $this->jsonFail([
                    '参数不完整'
                ]);
            }
        } elseif ($data['type'] == 'media_id' || $data['type'] == 'view_limited') {
            $data['media_id'] == Yii::$app->request->post('media_id');
            if (empty($data['media_id'])) {
                return $this->jsonFail([
                    '参数不完整'
                ]);
            }
        } elseif ($data['type'] == 'no') {} else {
            return $this->jsonFail('暂不支持其他菜单选择', '创建失败');
        }
        
        $msg = MenuService::Update($site_id, $data);
        if ($msg == 'SUCCESS') {
            return $this->jsonSuccess('', '修改成功');
        } else {
            return $this->jsonFail($msg, '修改失败');
        }
    }

    public function actionDel()
    {
        $site_id = $this->getSite();
        $id = Yii::$app->request->post('id');
        $msg = MenuService::Del($site_id, $id);
        if ($msg == 'SUCCESS') {
            return $this->jsonSuccess('', '删除成功');
        } else {
            return $this->jsonFail($msg, '删除失败');
        }
    }

    public function actionSort()
    {
        $site_id = $this->getSite();
        $data = Yii::$app->request->post('sort_list');
        if (empty($data)){
            return $this->jsonFail('参数缺失');
        }
        if (MenuService::Sort($site_id, $data)) {
            return $this->jsonSuccess(MenuService::GetbySite($site_id), '排序完成');
        } else {
            return $this->jsonSuccess(MenuService::GetbySite($site_id), '排序失败');
        }
    }

    public function actionShow()
    {
        $site_id = $this->getSite();
        $msg = MenuService::GetbySite($site_id);
        if (! empty($msg)) {
            return $this->jsonSuccess($msg, '查询成功');
        } else {
            return $this->jsonFail('', '未查询到');
        }
    }

    public function actionSubmit()
    {
        $site_id = $this->getSite();
        if ($site_id != 73) {
            return '暂时只允许site_id 73 测试菜单！';
        }
        $msg = MenuService::Submit($site_id);
        if (! empty($msg)) {
            return $this->jsonSuccess($msg, '微信端提交成功');
        } else {
            return $this->jsonFail('', '修改失败！请检查您的media_id或者url是否有效！！！');
        }
    }
}