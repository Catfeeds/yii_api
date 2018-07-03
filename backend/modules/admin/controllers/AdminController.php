<?php
namespace backend\modules\admin\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\admin\service\AdminService;
use backend\modules\sms\service\SMSService;
use backend\modules\sms\models\SmsLog;
use backend\modules\admin\models\Admin;
use backend\modules\admin\models\Site;
use backend\modules\admin\service\SiteService;

class AdminController extends BaseController
{

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
        if(empty($admin)){
            return $this->jsonFail([], '登陆失败');
        }
        $bool = Yii::$app->security->validatePassword($password, 
                $admin['password_hash']);
        if ($bool) {
            $admin->generateAccessToken();
            if ($admin->update()) {
                $site = SiteService::showmysite($admin['id']);
                return $this->jsonSuccess(
                        [
                                'site' => $site,
                                'user_name' => $admin->username,
                                'access_token' => $admin->access_token
                        ], '登陆成功');
            } else {
                // print_r($admin->getErrors());
                return $this->jsonFail([], '登陆失败');
            }
        } else {
            return $this->jsonFail([], '登陆失败');
        }
    }

    /**
     * 注册第一步 - 提交手机号码
     */
    public function actionRegisterstep1 ()
    {
        $mobile = Yii::$app->request->post('mobile');
        if (empty($mobile)) {
            return $this->jsonFail([], '参数不完整');
        }
        if (! empty(AdminService::findmobile($mobile))) {
            return $this->jsonFail([], '手机号已存在');
        }
        
        $status = SMSService::sendSmswithnum($mobile, SmsLog::STATUS_REGISTER);
        
        if ($status) {
            return $this->jsonSuccess([
                    'mobile' => $mobile
            ], '验证码已发送');
        } else {
            return $this->jsonFail([], '发送失败');
        }
    }

    /**
     * 第二步
     * 直接注册 - 提交手机号码、密码、验证码
     * 
     * @return access_token
     */
    public function actionRegisterstep2 ()
    {
        $data = Yii::$app->request->post();
        
        if (empty($data['mobile']) || empty($data['code'])) {
            return $this->jsonFail([], '请输入完整');
        }
        if (! empty(AdminService::findmobile($data['mobile']))) {
            return $this->jsonFail([], '手机号已存在');
        }
        if (strlen($data['password']) < 6) {
            return $this->jsonFail([], '密码太短');
        }
        $smsmessage = SMSService::validate($data['mobile'], 
                SmsLog::STATUS_REGISTER, $data['code']);
        if ($smsmessage['code'] != 0) {
            return $this->jsonFail([], $smsmessage['msg']);
        }
        
        if (! empty( $model = AdminService::addadmin($data['mobile'],$data['password']))) {
            return $this->jsonSuccess($model, '注册成功');
        } else {
            return $this->jsonFail([], '注册失败');
        }
    }

    // 修改找回密码 同过手机号验证
    // 流程1 同过用户名得到手机号码 发送验证短信
    public function actionFindpassword1 ()
    {
        $mobile = Yii::$app->request->post('mobile');
        if (empty($mobile)) {
            return $this->jsonFail([], '参数不完整');
        }
        $model = Admin::findByMobile($mobile);
        if (empty($model)) {
            return $this->jsonFail([], '未查询到手机号码');
        }
        
        $status = SMSService::sendSmswithnum($mobile, 
                SmsLog::STATUS_FINDPASSWORD);
        
        if ($status) {
            return $this->jsonSuccess([
                    'mobile' => $mobile
            ], '验证码已发送');
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
        // 暂用要先获取验证码
        // 测试可以去数据库pre_sms_log中添加数据
        $smsmessage = SMSService::validate($mobile, SmsLog::STATUS_FINDPASSWORD, 
                $code);
        if ($smsmessage['code'] != 0) {
            return $this->jsonFail([], $smsmessage['msg']);
        }
        if (! empty($model = AdminService::updatepassword($mobile, $password))) {
            return $this->jsonSuccess($model, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }
}
