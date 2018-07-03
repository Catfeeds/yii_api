<?php

/**用户注册流程
 */
namespace api\modules\v1\controllers;
use Yii;
use backend\modules\mall\models\User;
use api\base\BaseController;
use backend\modules\wechat\service\wechat;
use common\extensions\Wechat\WechatService;
use yii\helpers\Url;
use common\extensions\Wechat\WechatOauth;
use backend\modules\ucenter\models\Weixin_user;
use backend\modules\ucenter\service\UserService;

/*
 * 微信用户登陆
 */

class WxuserController extends BaseController
{

    /*
     * 微信用户登陆 获取用户的openid，openid唯一，同一主体下获取到unionid 是相同的
     * 但是不同主体的openid和unionid是不同的
     */
    
    //获取上一个页面带过来的url地址
    public function actionCode()
    {
        //获取code
        $config = wechat::getconfig();
        $url = urlencode(Yii::$app->request->get('url')); //业务url地址
        $wechat  = new WechatService();
        $access_url = urldecode(Yii::$app->request->hostInfo . Url::to(['login/access']));
        $openurl = $wechat -> getOauthRedirect($config['appid'],$access_url,'snsapi_userinfo',$url);
        if(empty($openurl)){
            return $this->jsonFail([],'获取失败');
        }
        return $this->jsonSuccess($openurl,'获取成功'); 
    }
    
    //获取access_token,openid
    public function actionAccess()
    {
        $code = new WechatOauth(1); //1站点id;
        $openid = $code -> getOauthAccessToken();  //获取token
        $wxuser = Weixin_user::findByOpenid($openid['openid']);
        if($wxuser){
            Yii::$app->session->set('openid',$wxuser->openid);
            Yii::$app->session->set('userid',$wxuser->uid);
        }else{
            $user = new UserService();
            $userinfo = $code -> getOauthUserInfo($openid['access_token'],$openid['openid']);
            $data = $user -> create($userinfo);
            Yii::$app->session->set('openid',$openid['openid']);
            Yii::$app->session->set('userid',$data);
        }
        $this->redirect(urldecode(Yii::$app->request->get('state')));
    }
    
}
