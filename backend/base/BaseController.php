<?php

/**
 * @date        : 2017年8月30日
 * @author      : Jason
 * @copyright   : http://www.hoge.cn/
 * @description : 后台控制器基类
 */
namespace backend\base;

use backend\modules\rabc\service\RabcService;
use yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use common\models\User;
use backend\modules\mall\models\Admin;
use backend\modules\admin\models\Site;

class BaseController extends Controller
{

    public $site;

    public $enableCsrfValidation = false;

    public $no_need_login;

    // 跨域
    public function beforeAction($action)
    {
        parent::actions($action);
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        // 判断当前的控制对应的方法需不需要登录验证
        if (in_array($this->module->id . '/' . $this->id . '/' . $this->action->id, $this->no_need_login) || in_array($this->module->id . '/' . $this->id . '/*', $this->no_need_login)) {
            return true;
        } else {
            $user_id = $this->getUserId();
            $site_id = $this->getSite();
            if (empty($user_id)) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $this->jsonFail([], '登陆失败', 2);
                return $this->jsonFail([], '登陆失败', 2);
            }
            if(!static::checkSite($user_id, $site_id)){
                return $this->jsonFail([], '非店铺管理员', 2);
            }
            //店铺判断
            //rbac判断

            /*
               * elseif(!empty($site_id)){
               * $site = SiteService::showsite($user_id, $site_id);
               * if(empty($site)){
               * Yii::$app->response->format = Response::FORMAT_JSON;
               * Yii::$app->response->data = $this->jsonFail([],'登陆他人店铺',3);
               * return false;
               * }
               * }
               */
        }
        return true;
    }

    // 初始化
    public function init()
    {
        parent::init();
        // 设置不需要登录的url
        
        $this->no_need_login = [
            'admin/admin/loginbypassword',
            'admin/admin/registerstep1',
            'admin/admin/registerstep2',
            'admin/admin/findpassword1',
            'admin/admin/findpassword2',
            'admin/site/notifyurl',
            'admin/site/returnurl',
            'attachment/image/*',
            'attachment/upload/uploadimageforueditor',
            'cms/article/index',
            'cms/article/',
            'restaurant/order/finishautomatic',
            'restaurant/order/errororder',
            'analysis/order/setsales'
            
        ];
    }

    protected function input()
    {
        return ArrayHelper::merge(Yii::$app->request->post(), Yii::$app->request->get());
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
        $page = empty($this->getParam('page')) ? 1 : $this->getParam('page');
        $my_page_info = array(
            'current_page' => (int) $page,
            'page_num' => (int) ($page_info->totalCount / $page_info->defaultPageSize) + 1,
            'total_num' => (int) $page_info->totalCount,
            'total_page' => (int) $page_info->defaultPageSize
        );
        return $my_page_info;
    }

    protected function isLoggedIn()
    {
        $access_token = Yii::$app->request->post('access_token');
        if (empty($access_token)) {
            $access_token = Yii::$app->request->get('access_token');
        }
        $user = Admin::findIdentityByAccessToken($access_token);
        return ! empty($user);
    }

    protected function getUserId()
    {
        $access_token = Yii::$app->request->post('access_token');
        if (empty($access_token)) {
            $access_token = Yii::$app->request->get('access_token');
        }
        $user = Admin::findIdentityByAccessToken($access_token);
        return $user ? $user->id : null;
    }

    protected function getUser()
    {
        $access_token = Yii::$app->request->post('access_token');
        if (empty($access_token)) {
            $access_token = Yii::$app->request->get('access_token');
        }
        return Admin::findIdentityByAccessToken($access_token);
    }

    protected function getSite()
    {
        $site_id = Yii::$app->request->post('site_id');
        if (empty($site_id)) {
            $site_id = Yii::$app->request->get('site_id');
        }
        return $site_id;
    }

    /*
     * protected function getAppCartCookieId()
     * {
     * return $this->getParam('app_cart_cookie_id') ?
     * $this->getParam('app_cart_cookie_id') : Cart::genAppCartCookieId();
     * }
     */
    protected function getParam($name, $defaultValue = null)
    {
        $defaultValue = Yii::$app->request->post($name);
        if (empty($defaultValue)) {
            $defaultValue = Yii::$app->request->get($name);
        }
        return $defaultValue;
        
        // return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $defaultValue;
    }
    /*
     * 店铺检查
     */
    protected function checkSite($user_id,$site_id)
    {
        return Site::find()->where(['site_id'=>$site_id,'user_id'=>$user_id])->count() > 0;
    }
}
