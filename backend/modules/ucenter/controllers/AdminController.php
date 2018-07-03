<?php
namespace backend\modules\ucenter\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\mall\models\Admin;
use common\extensions\aliyun_dysms\api_demo\SmsDemo;
use backend\modules\admin\service\SiteService;
use backend\modules\admin\service\AdminService;

class AdminController extends BaseController
{

    public function actionIndex ()
    {
        return $this->render('index');
    }

    // 使用手机号和密码登陆
    public function actionLoginbypassword ()
    {
        $mobile = Yii::$app->request->post('mobile');
        $password = Yii::$app->request->post('password');
        $model = new Admin();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {} else {
            return $this->jsonFail([], '手机号格式不正确');
        }
        if (! strlen($password)) {
            return $this->jsonFail([], '请输入密码');
        }
        $admin = Admin::findOneByMobile($mobile);
        
        $bool = Yii::$app->security->validatePassword($password, 
                $admin['password_hash']);
        if ($bool) {
            $admin->generateAccessToken();
            if ($admin->update()) {
                return $this->jsonSuccess(
                        [
                                'user_name' => $admin->username,
                                'access_token' => $admin->access_token
                        ], '登陆成功');
            } else {
                print_r($admin->getErrors());
                exit();
            }
        } else {
            return $this->jsonFail([], '登陆失败');
        }
    }

    // 修改找回密码 同过手机号验证
    // 流程1 同过用户名得到手机号码 发送验证短信
    public function actionFindpassword1 ()
    {
        $username = Yii::$app->request->post('username');
        $mobile = Yii::$app->request->post('mobile');
        if (empty($username) && empty($mobile)) {
            return $this->jsonFail([], '参数不完整');
        }
        $user = Admin::findbyusernameandmobile($username, $mobile);
        // 发送手机验证码
        $status = SmsDemo::sendSmswithnum($mobile, 2);
        if ($status) {
            return $this->jsonSuccess([
                    'mobile' => $mobile
            ], '发送成功');
        } else {
            return $this->jsonFail([], '发送失败');
        }
    }

    // 流程2 输入手机号 验证码 新密码
    public function actionFindpassword2 ()
    {
        $mobile = Yii::$app->request->post('mobile');
        $code = Yii::$app->request->post('code');
        $password = Yii::$app->request->post('password');
        if (empty($mobile) || empty($code) || empty($password)) {
            return $this->jsonFail([], '输入不完整');
        }
        if (strlen($password) < 6) {
            return $this->jsonFail([], '密码太短');
        }
        
        if (strlen($mobile) != 11) {
            return $this->jsonFail([
                    $mobile
            ], '手机号格式不正确');
        }
        // 暂用要先获取验证码
        // 测试可以去数据库pre_sms_log中添加数据
        $smsmessage = SmsDemo::validate($mobile, 2, $code);
        if ($smsmessage) {
            return $this->jsonFail([], $smsmessage['msg']);
        }
        $admin = $user = Admin::findbymobile($mobile);
        $hash_password = Yii::$app->security->generatePasswordHash($password);
        $admin->password_hash = $hash_password;
        $admin->generateAccessToken();
        $admin->mobile = $mobile;
        $admin->updated_at = time();
        if (! $admin->save()) {
            return array_values($admin->getFirstErrors())[0];
        }
        return $this->jsonSuccess($admin);
    }

    // 验证手机
    public function actionValidate ()
    {
        $mobile = $this->getParam('mobile');
        $code = $this->getParam('message');
        $id = $this->getParam('id');
        $status = SmsDemo::validate($mobile, $code);
        if ($status['code'] == 0) {
            if (Admin::updatemobile($id, $mobile)) {
                return $this->jsonSuccess([
                        $status
                ], '验证成功');
            } else
                return $this->jsonFail([], '验证失败');
        } else {
            return $this->jsonFail([], '验证失败');
        }
    }

    // 用户注册流程1
    public function actionRegisterstep1 ()
    {
        $mobile = $this->getParam('mobile');
        if (strlen($mobile) < 11) {
            return $this->jsonFail([], '手机号格式不正确');
        }
        
        $admin = Admin::findOneByMobile($mobile);
        if ($admin) {
            return $this->jsonFail([], '手机号已存在');
        }
        
        // 发送手机验证码
        // 暂时不许注册
        $status = SmsDemo::sendSmswithnum($mobile,1);
        
        if ($status) {
            return $this->jsonSuccess([
                    'mobile' => $mobile
            ], '发送成功');
        } else {
            return $this->jsonFail([], '发送失败');
        }
    }

    // 注册用户流程2
    public function actionRegisterstep2 ()
    {
        if (! ($this->getParam('password') || $this->getParam('mobile') ||
                 $this->getParam('code') || $this->getParam('username'))) {
            return $this->jsonFail([], '输入不完整');
        }
        if (! empty($this->getParam('password')) &&
                 ! empty($this->getParam('password2'))) {
            if ($this->getParam('password') != $this->getParam('password2'))
                return $this->jsonFail([], '两次密码不相同');
            if (strlen($this->getParam('password')) < 6) {
                return $this->jsonFail([], '密码太简单');
            }
        }
        $username = $this->getParam('username');
        $mobile = $this->getParam('mobile');
        $code = $this->getParam('code');
        if (! empty(Admin::findbyusername($username))) {
            return $this->jsonFail([], '用户名已注册');
        }
        if (strlen($mobile) != 11) {
            return $this->jsonFail([
                    $mobile
            ], '手机号格式不正确');
        }
        // 暂用要先获取验证码
        // 测试可以去数据库pre_sms_log中添加数据
        $smsmessage = SmsDemo::validate($mobile, 1, $code);
        if ($smsmessage) {
            return $this->jsonFail([], $smsmessage['msg']);
        }
        
        $admin = new Admin();
        $hash_password = Yii::$app->security->generatePasswordHash(
                $this->getParam('password'));
        $admin->username = $username;
        $admin->password_hash = $hash_password;
        $admin->generateAccessToken();
        $admin->mobile = $mobile;
        $admin->created_at = time();
        $admin->updated_at = time();
        if (! $admin->save()) {
            return array_values($admin->getFirstErrors())[0];
        }
        return $this->jsonSuccess($admin);
    }
}
