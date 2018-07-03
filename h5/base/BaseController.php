<?php

/**
 * @date        : 2017年8月30日
 * @author      : Jason
 * @copyright   : http://www.hoge.cn/
 * @description : 后台控制器基类
 */
namespace h5\base;

use yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use backend\modules\ucenter\models\User;
use backend\modules\admin\models\Site;
use backend\modules\ucenter\models\WeixinUser;
use backend\modules\admin\models\SiteWxconfig;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    public $no_need_login;
    
    public $check_work;

    protected function input()
    {
        return ArrayHelper::merge(Yii::$app->request->post(), Yii::$app->request->get());
    }

    public function init()
    {
        parent::init();
        // 设置不需要登录的url
        $this->no_need_login = [
            'login/*',
            'address/showreg',
            'order/paynotify',
            'order/print'
        ];
        // 设置需要检查在店铺工作url
        $this->check_work= [
            'order/create',
            'foodcar/caradd',
            'foodcar/cardel',
            'foodcar/cardel',
            'foodcar/carclear'
        ];
    }

    // 跨域
    public function beforeAction($action)
    {
        parent::actions($action);
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        
        // 判断当前的控制对应的方法需不需要登录验证
        if (in_array($this->id . '/' . $this->action->id, $this->no_need_login) || in_array($this->id . '/*', $this->no_need_login)) {
            // 如果不需要登录验证,就直接返回
            return true;
        }elseif (!$this->isLoggedIn()) {
            return $this->jsonFail('', '登陆失败', 2);
        }
        if(in_array($this->id . '/' . $this->action->id, $this->check_work) || in_array($this->id . '/*', $this->check_work)){
            if(! $this->SiteWork()){
                return $this->jsonFail('', '店铺休息！', 3);
            }
        }
        return true;
    }

    /**
     * api返回的json
     *
     * @param
     *            $status
     * @param
     *            $code
     * @param
     *            $message
     * @param
     *            $data
     * @param array $share            
     */
    protected function jsonSuccess($data = [], $message = '', $code = 0, $share = array())
    {
        $message = $message ? $message : '调用成功';
        $this->jsonEncode(true, $data, $message, $code, $share);
    }

    protected function jsonSuccessWithPage($data = [], $page_info = '', $message = '', $code = 0, $share = array())
    {
        $message = $message ? $message : '调用成功';
        $this->jsonEncodeWithPage(true, $data, $page_info, $message, $code, $share);
    }

    protected function jsonFail($data = [], $message = '', $code = 1, $share = array())
    {
        $message = $message ? $message : '调用失败';
        $this->jsonEncode(false, $data, $message, $code, $share);
    }

    protected function LoginFail($data = [], $message = '', $code = 2, $share = array())
    {
        $message = $message ? $message : '未登录/登陆失效';
        $this->jsonEncode(false, $data, $message, $code, $share);
    }

    protected function jsonEncode($status, $data = [], $message = '', $code = 0)
    {
        $status = boolval($status);
        $data = $data ? $data : (object) array();
        $message = strval($message);
        $code = intval($code);
        
        $result = [
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        
        // 设置响应对象
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $result;
    }

    protected function jsonEncodeWithPage($status, $data = [], $page_info = '', $message = '', $code = 0)
    {
        $status = boolval($status);
        $data = $data ? $data : (object) array();
        $page_info = $page_info ? $page_info : (object) array();
        $message = strval($message);
        $code = intval($code);
        $result = [
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'page_info' => $this->MyPageInfo($page_info),
            'data' => $data
        ];
        
        // 设置响应对象
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $result;
    }

    /**
     * 返回封装后的API数据到客户端
     *
     * @access protected
     * @param mixed $data
     *            要返回的数据
     * @param integer $code
     *            返回的code
     * @param mixed $msg
     *            提示信息
     * @param string $type
     *            返回数据格式
     * @param array $header
     *            发送的Header信息
     * @return void
     */
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => $_SERVER['REQUEST_TIME'],
            'data' => $data
        ];
        $type = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    protected function MyPageInfo($page_info = '')
    {
        $page = empty($this->getParam( 'page')) ? 1 : $this->getParam( 'page');
        $my_page_info = array(
            'current_page' => (int) $page,
            'page_num' => $page_info->getPageCount(),
            'total_num' => (int) $page_info->totalCount,
            'total_page' => (int) $page_info->defaultPageSize
        );
        return $my_page_info;
    }
    protected function isLoggedIn()
    {
    	$access_token= $this->getParam('access_token');
    	$user = User::findbytoken($access_token);
        return ! empty($user);
    }

    protected function getUserId()
    {
        $access_token= $this->getParam('access_token');
        $user = User::findbytoken($access_token);
        return $user->id ? $user->id : null;
    }

    protected function getUser()
    {
        $access_token= $this->getParam('access_token');
        return User::findbytoken($access_token);
    }

    protected function getSite()
    {
        return $this->getParam('site_id');
    }

    protected function SiteWork()
    {
        $site_id = $this->getParam('site_id');
        $site = Site::find()->where(['site_id'=>$site_id,'on_work'=>Site::ON_WORK])->count();
        if($site==0){
            return false;
        }else{
            return true;
        }
    }
    protected function getWxuser()
    {
        $openid = $this->getParam('openid');
        return WeixinUser::findOne(['openid'=>$openid]);
    }
    protected function getOpenId($user_id,$site_id)
    {
        if(empty(SiteWxconfig::findOne(['site_id'=>$site_id])))
        {
            $site_id = 2;
        }
        return WeixinUser::find()->select('openid')
        ->where([
            'uid' => $user_id,
            'site_id'=>$site_id
        ])->one()->openid;
    }
    protected function getParam($name, $defaultValue = null)
    {
    	$defaultValue = Yii::$app->request->post($name);
    	if (empty($defaultValue)) {
    		$defaultValue = Yii::$app->request->get($name);
    	}
    	return $defaultValue;
    	
    	// return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $defaultValue;
    }
}