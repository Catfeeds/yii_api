<?php
namespace backend\modules\admin\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\admin\service\SiteService;
use backend\modules\admin\models\SitePayOrder;
use backend\modules\admin\models\Site;

require_once '../../common/extensions/Alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
require_once '../../common/extensions/Alipay/pagepay/service/AlipayTradeService.php';

class SiteController extends BaseController
{
    // 显示我的店铺
    public function actionMysite ()
    {
        $user_id = $this->getUserId();
        $sites = SiteService::showmysite($user_id);
        if (empty($sites)) {
            return $this->jsonFail([], '未查询到您的店铺');
        } else {
            return $this->jsonSuccess($sites, '查询成功');
        }
    }
    public function actionShowwork()
    {
        $site_id = $this->getSite();
        if(!empty($model = SiteService::show_work($site_id))){
            return $this->jsonSuccess($model,'查询成功');
        }
        return $this->jsonFail('','查询失败');
    }
    public function actionTurnwork()
    {
        $site_id = $this->getSite();
        if(SiteService::turn_work($site_id)){
            return $this->jsonSuccess(SiteService::show_work($site_id),'开关成功');
        }
        return $this->jsonFail('','开关失败');
    }
    //店铺续费
    public function actionPay()
    {
        $user_id = $this->getUserId();
        $site_id = $this->getSite();
        $time = Yii::$app->request->post('time');
        if(empty($time)){
            $time = 31536000;//一年 暂设定 以后通过月份计算
        }
        $site = SiteService::showsite($user_id, $site_id);
        if(empty($site)){
            return $this->jsonFail('','请登录正确的账号');
        }
        $site_order = new SitePayOrder();
        $site_order->order_sn = time().rand(10000,99999);
        $site_order->admin = $user_id;
        $site_order->site = $site_id;
        $site_order->price = SiteService::payprice($site_id, $time);
        $site_order->create_at = time();
        $site_order->pay_code = 0;//暂时用0代替支付宝
        $site_order->time = $time;
        $site_order->save();
        
        //支付过程
        $alipay = new \AlipayTradePagePayContentBuilder();
        $alipay->setOutTradeNo($site_order->order_sn);
        $alipay->setTotalAmount($site_order->price);
        $alipay->setSubject(Yii::$app->params['our']);
        
        $config = Yii::$app->params['alipay'];
        
        $servicOBj = new \AlipayTradeService($config);
        $result = $servicOBj -> pagePay($alipay,$config['return_url'], $config['notify_url']);
        
        return $result;
    }
    //异步回掉地址
    public function actionNotifyurl()
    {
        $data = Yii::$app->request->post();
        $config = Yii::$app->params['alipay'];
        
        $servicOBj = new \AlipayTradeService($config);
        $result = $servicOBj->check($data);
        if($result){
            if($data['trade_status']==="TRADE_SUCCESS"){
                $site_order = SitePayOrder::findOne(['order_sn'=>$data['out_trade_no']]);
                if(!empty($site_order)){
                    $site_order->pay_status = 1;
                    $site_order->order_status = 1;
                    $time = $site_order->time;
                    $site_id = $site_order->site;
                    if($site_order->save()&&SiteService::addexpires($site_id, $time)){
                        echo("success");
                        exit();
                    }
                }
            }
            return $this->jsonSuccess('','支付失败');
        }else{
            return $this->jsonFail('','支付失败');
        }
    }
    //同步回掉
    public function actionReturnurl()
    {
        $data = Yii::$app->request->get();
        $config = Yii::$app->params['alipay'];
        
        $servicOBj = new \AlipayTradeService($config);
        $result = $servicOBj->check($data);
        
        if($result){
            return $this->jsonSuccess($result,'支付成功');
        }else{
            return $this->jsonFail('','支付失败');
        }
    }
    // 修改我的店铺信息
    public function actionUpdatesite ()
    {
        $user_id = $this->getUserId();
        $site_id = $this->getSite();
        $name = Yii::$app->request->post('name');
        $logo = Yii::$app->request->post('logo');
        $image = Yii::$app->request->post('image');
        $date = Yii::$app->request->post( );
        $model = SiteService::updatesite($user_id, $site_id,$name,$logo,$image,$date);
        
//         if (!empty($model)) {
        if ($model) {
            return $this->jsonSuccess($model, '修改成功');
        } else {
            return $this->jsonFail([], '修改失败');
        }
    }
    
    // 显示我的店铺具体信息
    public function actionMysiteone ()
    {
        $user_id = $this->getUserId();
        $site_id = $this->getSite();
        if (empty($model = SiteService::showsite($user_id, $site_id))) {
            return $this->jsonFail([], '未查询到您的店铺');
        } else {
            return $this->jsonSuccess($model, '查询成功');
        }
    }
    
    public function actionShipconfig()
    {
        $site_id = $this->getSite();
        if(empty($site_id)){
            return $this->jsonFail();
        }
        $shipping_price = Yii::$app->request->post('shipping_price');
        $offer_price = Yii::$app->request->post('offer_price');
        
        if(SiteService::priceConfig($site_id, $shipping_price, $offer_price)){
            return $this->jsonSuccess('','设置起送价格成功！');
        }else{
            return $this->jsonFail('','设置失败！');
        }
    }
    
    public function actionGetship()
    {
        $site_id = $this->getSite();
        $ship = SiteService::Shipprice($site_id);
        if(!empty($ship)){
            return $this->jsonSuccess($ship,'查询成功');
        }else{
            return $this->jsonFail($ship,'查询失败');
        }
    }
}
