<?php

/**用户注册流程
 */
namespace h5\modules\v1\controllers;

use Yii;
use h5\base\BaseController;
use common\extensions\Wechat\WechatScript;

class WxconfigController extends BaseController {
    
    public function actionGetwxconfig()
    {
        $site_id = $this->getSite();
        if(empty($site_id)){
            $site_id = 2;
        }
        $signpackage = new WechatScript($site_id);
        $url = Yii::$app->request->get('url');
        //$header = Yii::$app->request->headers;
        //$url = $header['referer'];
        //$url = "http://h5.demo-xn.itdongli.com/?order_sn=15248279247046&order_id=404";
        
        $sign = $signpackage->getJsSign(urldecode($url));
		return $this->jsonSuccess($sign);
    }
}
