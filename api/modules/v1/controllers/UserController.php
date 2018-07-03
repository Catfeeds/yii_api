<?php

/**用户注册流程
 */
namespace api\modules\v1\controllers;
use Yii;
use backend\modules\mall\models\User;
use api\base\BaseController;
use backend\modules\wechat\service\wechat;
use common\extensions\xcx\WeApp;
use backend\modules\ucenter\models\WeixinUser;
use api\modules\v1\service\UserService;

class UserController extends BaseController
{
    //通过code获取微信用户的openid
    public function actionGetcode()
    {
        $config = wechat::getconfig(null,1);
        $wxapp = new WeApp($config['appid'], $config['appsecret'], "");
        $code = Yii::$app->request->get('js_code');
        $model = json_decode($wxapp->getSessionKey($code),true);
        $user = WeixinUser::findByOpenid($model['openid']);
        if(!empty($user)){
            return $this->jsonSuccess($model,'登陆成功');
        }else{
            return $this->jsonFail($model,'第一次登陆 申请获取用户信息',2001);
        }
    }
    //创建用户
    public function actionCreatuser()
    {
        $userinfo = Yii::$app->request->post('userinfo');
        $session_id = Yii::$app->request->post('session_id');
        $openid = $session_id['openid'];
        if(empty($userinfo)||empty($openid))
        {
            return $this->jsonFail('','参数不完整');
        }
        if(!empty(UserService::getUser($openid))){
            return $this->jsonFail([],'用户已经创建');
        }
        $user = new UserService();
        $data = $user -> create($userinfo,$openid);
        $session_id = shell_exec("head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168");
        Yii::$app->cache->add($session_id, $openid);
        return $this->jsonSuccess($session_id,'首次登陆成功');
    }
    //通过code获取微信用户的openid
    //通过openid和session_key 来生成3rd_session为key的键值对，以缓存的形式存储。
    //返回3rd_session。
    public function actionLogin()
    {
        $config = wechat::getconfig(null,1);
        $wxapp = new WeApp($config['appid'], $config['appsecret'], "");
        $code = Yii::$app->request->get('js_code');
        $model = json_decode($wxapp->getSessionKey($code),true);
        $user = WeixinUser::findByOpenid($model['openid']);
        if(!empty($user)){
            $session_id = shell_exec("head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168");
            Yii::$app->cache->add($session_id, $model['openid']);
            return $this->jsonSuccess($session_id,'登陆成功');
        }else{
            return $this->jsonFail($model,'第一次登陆 申请获取用户信息',2001);
        }
    }
}
