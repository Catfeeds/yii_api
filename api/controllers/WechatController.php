<?php
//微信回调地址
namespace api\controllers;
use Yii;
use api\base\BaseApiController;
use common\extensions\Wechat\lib\Common;
use common\extensions\Wechat\WechatUser;
use backend\modules\wechat\models\menu;
use backend\modules\wechat\controllers\SiteController;
use common\extensions\Wechat\WechatReceive;

class WechatController extends BaseApiController
{
    public $modelClass = 'backend\modules\content\models\Category';
    public $enableCsrfValidation = false;
    public $site_key;
    public function actions()
    {
        $options = SiteController::actionSiteview();
        \common\extensions\Wechat\Loader::config($options);
    }

    public function actionIndex($site_id)
    {
        $content = file_get_contents ( 'php://input' );
        !empty ( $content ) || die ( '这是微信请求的接口地址，直接在浏览器里无效' );
        $data = new WechatReceive();
        $data -> valid();
        $msgtype = $data->getRev()->getRevType();
        switch ($msgtype){
            case $data::MSGTYPE_TEXT:
                if($data->getRevContent() == '电话'){
                    $data->text('13925896587')->reply();
                }else{
                    $data->text('你好,你在说什么')->reply();
                }
                break;
            case $data::MSGTYPE_EVENT:
                $data->text('事件')->reply();
                break;
            case $data::MSGTYPE_IMAGE:
                $data->text('图片')->reply();
                break;
            default:
                $data->text('不知道发的什么')->reply();
        }
    }

}
