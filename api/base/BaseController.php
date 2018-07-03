<?php

/**
 * @date        : 2017年8月30日
 * @author      : Jason
 * @copyright   : http://www.hoge.cn/
 * @description : 后台控制器基类
 */
namespace api\base;

use yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use backend\modules\mall\models\User;
use api\modules\v1\service\UserService;
use yii\helpers\Url;

class BaseController extends Controller {

	public $enableCsrfValidation = false;
	public $no_need_login;
	protected function input() {
		return ArrayHelper::merge ( Yii::$app->request->post (), Yii::$app->request->get () );
	}
	public function init() {
		parent::init ();
		// 设置不需要登录的url
		$this->no_need_login = [ 
				'user/*',
				'region/*',
		        'foodorder/paynotify'

		];
	}
	
	// 跨域
	public function beforeAction($action) {
		parent::actions ( $action );
		header ( 'Access-Control-Allow-Origin:*' );
		header ( 'Access-Control-Allow-Methods:POST' );
		header ( 'Access-Control-Allow-Headers:x-requested-with,content-type' );
		// 判断当前的控制对应的方法需不需要登录验证
		if (in_array ( $this->id . '/' . $this->action->id, $this->no_need_login ) || in_array ( $this->id . '/*', $this->no_need_login )) {
			// 如果不需要登录验证,就直接返回
			return true;
		}
		$user_id = $this->getUserId ();
		if (! $user_id) {
			return $this->jsonFail ( '', '登陆失败', 2 );
		}
		return true;
	}
	
	/**
	 * api返回的json
	 *
	 * @param
	 *        	$status
	 * @param
	 *        	$code
	 * @param
	 *        	$message
	 * @param
	 *        	$data
	 * @param array $share        	
	 */
	protected function jsonSuccess($data = [], $message = '', $code = 0, $share = array()) {
		$message = $message ? $message : '调用成功';
		$this->jsonEncode ( true, $data, $message, $code, $share );
	}
	protected function jsonSuccessWithPage($data = [], $page_info = '', $message = '', $code = 0, $share = array()) {
		$message = $message ? $message : '调用成功';
		$this->jsonEncodeWithPage ( true, $data, $page_info, $message, $code, $share );
	}
	protected function jsonFail($data = [], $message = '', $code = 1, $share = array()) {
		$message = $message ? $message : '调用失败';
		$this->jsonEncode ( false, $data, $message, $code, $share );
	}
	protected function LoginFail($data = [], $message = '', $code = 2, $share = array()) {
		$message = $message ? $message : '未登录/登陆失效';
		$this->jsonEncode ( false, $data, $message, $code, $share );
	}
	protected function jsonEncode($status, $data = [], $message = '', $code = 0) {
		$status = boolval ( $status );
		$data = $data ? $data : ( object ) array ();
		$message = strval ( $message );
		$code = intval ( $code );
		
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
	protected function jsonEncodeWithPage($status, $data = [], $page_info = '', $message = '', $code = 0) {
		$status = boolval ( $status );
		$data = $data ? $data : ( object ) array ();
		$page_info = $page_info ? $page_info : ( object ) array ();
		$message = strval ( $message );
		$code = intval ( $code );
		$result = [ 
				'status' => $status,
				'code' => $code,
				'message' => $message,
				'page_info' => $this->MyPageInfo ( $page_info ),
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
	 *        	要返回的数据
	 * @param integer $code
	 *        	返回的code
	 * @param mixed $msg
	 *        	提示信息
	 * @param string $type
	 *        	返回数据格式
	 * @param array $header
	 *        	发送的Header信息
	 * @return void
	 */
	protected function result($data, $code = 0, $msg = '', $type = '', array $header = []) {
		$result = [ 
				'code' => $code,
				'msg' => $msg,
				'time' => $_SERVER ['REQUEST_TIME'],
				'data' => $data 
		];
		$type = $type ?: $this->getResponseType ();
		$response = Response::create ( $result, $type )->header ( $header );
		throw new HttpResponseException ( $response );
	}
	protected function MyPageInfo($page_info = '') {
	    $page = empty ( $this->getParam( 'page' ) ) ? 1 :  $this->getParam( 'page' );
		$my_page_info = array (
				'current_page' => ( int ) $page,
		          'page_num' => $page_info->getPageCount(),
				'total_num' => ( int ) $page_info->totalCount,
				'total_page' => ( int ) $page_info->defaultPageSize 
		);
		return $my_page_info;
	}
	protected function isLoggedIn() {
		$session_id = Yii::$app->request->post ( 'session_id' );
		if (empty ( $session_id )) {
			$session_id = Yii::$app->request->get ( 'session_id' );
		}
		if (empty ( $session_id )) {
			return null;
		}
		$openid = Yii::$app->cache->get ( $session_id );
		if (empty ( $openid )) {
			return null;
		}
		$user = UserService::getUser ( $openid );
		return ! empty ( $user );
	}
	protected function getOpenId() {
		$session_id = Yii::$app->request->post ( 'session_id' );
		if (empty ( $session_id )) {
			$session_id = Yii::$app->request->get ( 'session_id' );
		}
		if (empty ( $session_id )) {
			return null;
		}
		$openid = Yii::$app->cache->get ( $session_id );
		return $openid ? $openid : null;
	}
	protected function getUserId() {
		$session_id = Yii::$app->request->post ( 'session_id' );
		if (empty ( $session_id )) {
			$session_id = Yii::$app->request->get ( 'session_id' );
		}
		if (empty ( $session_id )) {
			return null;
		}
		$openid = Yii::$app->cache->get ( $session_id );
		if (empty ( $openid )) {
			return null;
		}
		$user = UserService::getUser ( $openid );
		return $user->id ? $user->id : null;
	}
	protected function getUser() {
		$session_id = Yii::$app->request->post ( 'session_id' );
		if (empty ( $session_id )) {
			$session_id = Yii::$app->request->get ( 'session_id' );
		}
		if (empty ( $session_id )) {
			return null;
		}
		$openid = Yii::$app->cache->get ( $session_id );
		if (empty ( $openid )) {
			return null;
		}
		return $user = UserService::getUser ( $openid );
	}
	protected function getSite() {
		$site_id = Yii::$app->request->post ( 'site_id' );
		if (empty ( $site_id )) {
			$site_id = Yii::$app->request->get ( 'site_id' );
		}
		return $site_id;
	}
	protected function getParam($name, $defaultValue = null) {
		$defaultValue = Yii::$app->request->post ( $name );
		if (empty ( $defaultValue )) {
			$defaultValue = Yii::$app->request->get ( $name );
		}
		return $defaultValue;
		
		// return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $defaultValue;
	}
}