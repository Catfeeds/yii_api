<?php
namespace backend\modules\restaurant\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\TablesService;

class TablesController extends BaseController
{
    public function actionTest()
    {
        $background = '../../web/images/Qr/background.png';
        readfile($background);
    }
    // 获取店铺内所有桌号
    public function actionIndex ()
    {
        $siteid = $this->getSite();
        if(empty($siteid)){
            return $this->jsonFail([],'参数缺失');
        }
        $tables = TablesService::showbysite($siteid);
        if (empty($tables)) {
            return $this->jsonFail([], '未查询到');
        } else {
            return $this->jsonSuccess($tables, '查询成功');
        }
    }

    // 创建新桌号
    public function actionCreate ()
    {
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if(!empty($model = TablesService::create($data))){
            return $this->jsonSuccess($model,'创建成功');
        }else{
            return $this->jsonFail([],'创建失败');
        }
    }
    //创建二维码
    //多个table时创建压缩包
    //单个桌子创建单图片
    public function actionCreateqr()
    {
        $tables = Yii::$app->request->get('tables');
        $ids = array_filter(explode(",",$tables));
        if(count($ids)>1){
            $model = TablesService::createQr($ids);
            if(empty($model)){
                return $this->jsonFail([],'查询失败');
            }
            $filename = time().'.zip';
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Type: application/zip');
            header("Accept-Ranges: bytes");
            //这里对客户端的弹出对话框，对应的文件名
            header("Content-Disposition: attachment; filename=".$filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '. filesize($model));
            readfile($model);
            unlink($model);
        }
        else{
            $model = TablesService::createOneQr($ids[0]);
            if(empty($model)){
                return $this->jsonFail([],'查询失败');
            }
            header('Cache-control: max-age=3600');
            header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600) . ' GMT');
            $imageinfo = getimagesize($model);
            $filename = TablesService::getname($ids[0]).'.png';
            header('Content-Type: '.$imageinfo['mime']);
            header('Content-Transfer-Encoding: binary');
            header("Content-Disposition: attachment; filename=".$filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '. filesize($model));
            readfile($model);
        }
    }
    // 修改桌位
    public function actionUpdate ()
    {
        // 这里要判断是否为本店铺管理员。
        $data = Yii::$app->request->post();
        if(empty($data['table_id'])){
            return $this->jsonFail([],'参数缺失');
        }
        if(!empty($model = TablesService::update($data))){
            return $this->jsonSuccess($model,'修改成功');
        }else{
            return $this->jsonFail([],'修改失败');
        }
    }
    // 删除桌位
    public function actionDelete ()
    {
        // 这里要判断是否为本店铺管理员。
        $table_id= Yii::$app->request->get('table_id');
        $site_id= $this->getSite();
        if(TablesService::delete($table_id,$site_id)){
            return $this->jsonSuccess($table_id,'删除成功');
        }else{
            return $this->jsonFail([],'删除失败');
        }
    }
}