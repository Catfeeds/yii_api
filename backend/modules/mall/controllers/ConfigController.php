<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\base\BaseController;
use AliyunMNS\Config;

class ConfigController extends BaseController
{

    // 显示所有
    public function actionIndex ()
    {
        $configs = Config::find()->all();
        return $this->jsonSuccess($configs, '查询成功');
    }

    // 通过config_id
    public function actionFindbyconfigid ()
    {
        $id = $this->getParam('config_id');
        if (empty($id)) {
            return $this->jsonFail([], '参数不完整');
        }
        $config = Config::find()->where('id = :id', [
                ':id' => $id
        ])->one();
        
        return $this->jsonSuccess($config, '查询成功');
    }

    // 修改config title不可修改
    public function actionUpdate ()
    {
        $id = Yii::$app->request->post('config_id');
        $keywords = Yii::$app->request->post('keywords');
        $logo_id = Yii::$app->request->post('logo_id');
        $description = Yii::$app->request->post('description');
        $copyright = Yii::$app->request->post('copyright');
        $theme = Yii::$app->request->post('theme');
        if (empty($id) || empty($keywords) || empty($logo_id) ||
                 empty($description) || empty($copyright) || empty($theme)) {
            return $this->jsonFail([], '参数不完整');
        }
        $config = Config::find()->where('id = :id', [
                ':id' => $id
        ])->one();
        $config->keywords = $keywords;
        $config->logo_id = $logo_id;
        $config->description = $description;
        $config->copyright = $copyright;
        $config->theme = $theme;
        if ($config->save) {
            return $this->jsonSuccess($config, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }

    // 创建config
    public function actionCreate ()
    {
        $id = Yii::$app->request->post('config_id');
        $title = Yii::$app->request->post('title');
        $keywords = Yii::$app->request->post('keywords');
        $logo_id = Yii::$app->request->post('logo_id');
        $description = Yii::$app->request->post('description');
        $copyright = Yii::$app->request->post('copyright');
        $theme = Yii::$app->request->post('theme');
        if (empty($id) || empty($title) || empty($keywords) || empty($logo_id) ||
                 empty($description) || empty($copyright) || empty($theme)) {
            return $this->jsonFail([], '参数不完整');
        }
        $config = new Config();
        $config->keywords = $keywords;
        $config->logo_id = $logo_id;
        $config->description = $description;
        $config->copyright = $copyright;
        $config->theme = $theme;
        if ($config->save) {
            return $this->jsonSuccess($config, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }
}
