<?php
/**
 * @author Jason
 * 用户登录
 */
namespace h5\modules\v1\controllers;

use Yii;
use h5\base\BaseController;
use backend\modules\ucenter\models\User;
use backend\modules\sms\service\SMSService;
use backend\modules\sms\models\SmsLog;
use common\extensions\Wechat\WechatService;
use common\extensions\Wechat\WechatOauth;
use backend\modules\ucenter\models\WeixinUser;
use yii\base\Security;
use api\modules\v1\models\FoodCar;
use api\modules\v1\models\FoodCarNum;
use backend\modules\mall\models\Address;
use backend\modules\wechat\service\wechat;


class LoginController extends BaseController
{
    public $enableCsrfValidation = false;
    
    public function actionRegister1()
    {
        $mobile = Yii::$app->request->post('mobile');
        if (empty($mobile)) {
            return $this->jsonFail([], '参数不完整');
        }
//         if (! empty(User::findByMobile($mobile))) {
//             return $this->jsonFail([], '手机号已存在');
//         }
        
        $status = SMSService::sendSmswithnum($mobile, SmsLog::STATUS_REGISTER);
        
        if ($status) {
            return $this->jsonSuccess([
                'mobile' => $mobile
            ], '验证码已发送');
        } else {
            return $this->jsonFail([], '发送失败');
        }
    }
    public function actionRegister2 ()
    {
        $data = Yii::$app->request->post();
        $site_id = $this->getSite();
        if(empty($site_id)){
            $site_id = 2;
        }
        
        if (empty($data['mobile']) || empty($data['code'])) {
            return $this->jsonFail([], '请输入完整');
        }
        
//         if (! empty(User::findByMobile($data['mobile']))) {
//         	return $this->jsonFail([], '手机号已存在');
//         }
        
        if (strlen($data['password']) < 6) {
            return $this->jsonFail([], '密码太短');
        }
        $smsmessage = SMSService::validate($data['mobile'],
            SmsLog::STATUS_REGISTER, $data['code']);
        if ($smsmessage['code'] != 0) {
            return $this->jsonFail([], $smsmessage['msg']);
        }
        $user = User::findByMobile($data['mobile']);
        if (empty($user)){
            $user = new User();
            $user->mobile = $data['mobile'];
            $user->created_at = time();
        }
        $user->setPassword($data['password']); 
        $user->updated_at = time();
        $user->generateAccessToken();

        if ($user->save()) {
            $wxuser_id = $this->getUser()->id;
            $wxuser = WeixinUser::findOne(['uid'=>$wxuser_id]);
            $old_user = User::findOne(['id'=>$wxuser_id]);
            if($old_user->mobile == null)
            {
                $wxuser->uid = $user->id;
                $wxuser->site_id = $site_id;
                $wxuser->save();
                
                $old_user->delete();
                
                $foodcar = new FoodCar();
                $foodcar->updateAll(['user_id'=>$user->id],'user_id ='.$wxuser_id);
                
                $foodcarnum = new FoodCarNum();
                $foodcarnum->updateAll(['user_id'=>$user->id],'user_id ='.$wxuser_id);
                
                $address = new Address();
                $address->updateAll(['user_id'=>$user->id],'user_id ='.$wxuser_id);
            }
            if(!empty($wxuser)){
                $wxuser->uid = $user->id;
                $wxuser->save();
            }
            return $this->jsonSuccess($user->access_token, '绑定成功');
        } else {
            return $this->jsonFail([], '绑定失败');
        }
    }
    public function actionLogin()
    {
        
        $mobile = Yii::$app->request->post('mobile');
        $password = Yii::$app->request->post('password');
        $site_id = $this->getParam('site_id');
        $user = User::findOne(['mobile'=>$mobile]);
        if(empty($user)){
            return $this->jsonFail('','未查询到用户');
        }
        if(Yii::$app->security->validatePassword($password,$user->password_hash)){
            $user->getAccessToken();
            if($user->save()){
                return $this->jsonSuccess($user->access_token,'登陆成功');
            }
        }
        return $this->jsonFail('','登陆失败');
    }
    
    public function actionCode()
    {
        $site_id = $this->getSite();
        if(empty($site_id)) $site_id = 2;//暂用
        //$config = Yii::$app->params['wechat_gzh'];
        
        $config= wechat::getconfig($site_id);
        $wechat = new WechatService($config);
        $access_url = Yii::$app->params['Access_url'].'?site_id='.$site_id;
        //$state = urlencode(Yii::$app->request->get('url'));
        $state = Yii::$app->params['h5_url'].'?site_id='.$site_id;
        $openurl = $wechat->getOauthRedirect($config['appid'], $access_url,'snsapi_userinfo',$state);
        return $openurl;
        //$this->redirect($openurl);
    }
    
    public function actionAccess()
    {
        $site_id = $this->getSite();
        if(empty($site_id)){
            $site_id = 2;
        }
        
        $code = new WechatOauth($site_id); //站点id;
        $openid = $code -> getOauthAccessToken();  //获取token
        $wxuser = WeixinUser::findByOpenid($openid['openid']);
        if(empty($wxuser)){
            $user = new User();
            $user->mobile = null;
            $security = new Security();
            $randomString = $security->generateRandomString(12);
            $user->setPassword($randomString);
            $user->created_at = time();
            $user->updated_at = time();
            $user->generateAccessToken();
            $user->save();
            $userinfo = $code -> getOauthUserInfo($openid['access_token'],$openid['openid']);
            $wxuser = new WeixinUser();
            $wxuser -> openid = $openid['openid'];
            $wxuser -> uid = $user->id;
            $wxuser -> site_id = $site_id;
            $wxuser -> create_time = time();
            $wxuser -> headimgurl = $userinfo['headimgurl'];
            $wxuser -> nickname = $userinfo['nickname'];
            $wxuser -> save();
        }
        $user = User::findOne(['id'=>$wxuser->uid]);
        $user->getAccessToken();
        $user->save();
        $access_token = $user->access_token;
        
        //header('HTTP/1.1 301 Moved Permanently');//发出301头部
        //header('Location: '.urldecode(Yii::$app->request->get('state').'?access_token='.$access_token));//跳转到我的新域名地址
        
        $this->redirect(urldecode(Yii::$app->request->get('state').'#/home/goods?access_token='.$access_token));
        //$this->redirect($openurl.'&access_token='.$access_token);
        //return $this->jsonSuccess($access_token,'微信登陆成功');
    }
    
    public function actionBindwx()
    {
        $user = $this->getUserId();
        $wx = $this->getWxuser();
        if($wx->uid!=0){
            return $this->jsonFail('','用户已经绑定');
        }else{
            $wx->uid = $user;
            if($wx->save()){
                return $this->jsonSuccess('','绑定成功');
            }
        }
    }
    /*
    //获取上一个页面带过来的url地址
    public function actionCode()
    {
        //获取code
        $config = wechat::getconfig();
        $url = urlencode(Yii::$app->request->get('url')); //业务url地址
        $wechat  = new WechatService();
        $access_url = urldecode(Yii::$app->request->hostInfo . Url::to(['login/access']));
        $openurl = $wechat -> getOauthRedirect($config['appid'],$access_url,'snsapi_userinfo',$url);
        $this->redirect($openurl);
    }

    //获取access_token,openid
    public function actionAccess()
    {
        $code = new WechatOauth(1); //1站点id;
        $openid = $code -> getOauthAccessToken();  //获取token
        $wxuser = WeixinUser::findByOpenid($openid['openid']);
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
    */
}
