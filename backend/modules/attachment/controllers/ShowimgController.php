<?php
namespace backend\modules\attachment\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\attachment\models\Attachment;
use yii\data\Pagination;
use backend\modules\attachment\service\ShowimgService;

class ShowimgController extends BaseController
{
    public function actionShowmypic ()
    {
        $site_id = $this->getSite();
        $pagination = new Pagination([
                        'totalCount' => ShowimgService::mypiccount($site_id),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam(
                                'per-page')
                ]);
        $pagination->setPage($this->getParam('page')-1);
        $attachment = ShowimgService::showmypic($site_id, $pagination->offset, $pagination->limit);
        if (empty($attachment)) {
            return $this->jsonFail([], '未查询到');
        }
        return $this->jsonSuccessWithPage($attachment, $pagination);
    }

    public function actionShowmycatpic ()
    {
        $site_id = $this->getSite();
        $catid = Yii::$app->request->get('catid');
        $filename = Yii::$app->request->get('name');
        if(empty($site_id)){
            return $this->jsonFail([],'参数缺失');
        }if(empty($catid)){
            $catid = 0;
        }
        $pagination = new Pagination(
                [
                        'totalCount' => ShowimgService::mypicwithcatidcount($site_id, $catid,$filename),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '10' : $this->getParam(
                                'per-page')
                ]);
        $pagination->setPage($this->getParam('page')-1);
        $attachment = ShowimgService::showmypicwithcatid($site_id, $catid,$filename, $pagination->offset, $pagination->limit);
        if (empty($attachment)) {
            return $this->jsonFail([], '未查询到');
        }
        return $this->jsonSuccessWithPage($attachment, $pagination);
    }

    public function actionShowthepic ()
    {
        $aid = Yii::$app->request->get('aid');
        $attachment = ShowimgService::showpic($aid);
        if (empty($attachment)) {
            return $this->jsonFail([], '未查询到');
        }
        return $this->jsonSuccess($attachment, '查询成功');
    }

    public function actionUpdate ()
    {
        $aid = Yii::$app->request->post('aid');
        $catid = Yii::$app->request->post('catid');
        $name = Yii::$app->request->post('name');
        if(empty($catid)&&empty($name)){
            return $this->jsonFail([],'参数缺失');
        }
        if (ShowimgService::updatepic($aid,$catid,$name)) {
            return $this->jsonSuccess($aid, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }

    public function actionDelete ()
    {
        $aid = Yii::$app->request->post('aid');
        if (ShowimgService::deletethis($aid)) {
            return $this->jsonSuccess([], '删除成功');
        } else {
            return $this->jsonFail([], '删除失败');
        }
    }
}
