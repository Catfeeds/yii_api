<?php
//微信手机网页支付回调
namespace api\controllers;

use Yii;
use yii\web\Controller;

class WxpayController extends Controller{
    public $enableCsrfValidation  = false;

    public $openid;
    public $out_trade_no;

    public function actionNotify(){
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($postObj === false) {
            die('parse xml error');
        }
        if ($postObj->return_code != 'SUCCESS') {
            die($postObj->return_msg);
        }
        if ($postObj->result_code != 'SUCCESS') {
            die($postObj->err_code);
        }

        //更新订单信息
        $openid=$postObj->openid;
        $out_trade_no=$postObj->out_trade_no;
        $order = Order::find()->where(['openid'=>$openid, 'out_trade_no'=>$out_trade_no])->one();

        if($order){
            $order->status=2;
            $order->out_trade_no=$out_trade_no;
            $data = $order->update();
        }

        //添加一条VIP记录
        $user_check = Member::find()->where(['openid'=>$openid ])->one();
        if($user_check){
            $user_check->vip= '1';
            $user_check->created_at=time();
            $user_check->update();
            $userid=tools::getuserid($openid);
            $this->actionAddactivity($userid);
        }else{
            $vipmem = new Member();
            $vipmem->openid = $openid;
            $vipmem->vip='1';
            $vipmem->created_at=time();
            $vipmem->save();
            $vipid=$vipmem->attributes['id']; //vip的id
            $userid=tools::getuserid($openid);
            $this->actionAddactivity($userid);
        }
        return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

    //添加推广记录
    /*$userid  当前用户iduserid
     *$ancestry  父id
     * */
    public function actionAddactivity($userid){
        $ancestry = TreeController::actionChild($userid);
        $result = TreeController::actioinGetparent($userid,$ancestry);
    }


}