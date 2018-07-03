<?php
// 微信手机网页支付回调
namespace backend\modules\mall\controllers;
use Yii;
use backend\modules\mall\models\OrderAction;
use backend\modules\mall\models\Order;
use backend\base\BaseController;

class WxpayController extends BaseController
{

    public $enableCsrfValidation = false;

    public $openid;

    public $out_trade_no;

    public function actionNotify ()
    {
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', 
                LIBXML_NOCDATA);
        // 错误抛出
        if ($postObj === false) {
            die('parse xml error');
        }
        if ($postObj->return_code != 'SUCCESS') {
            die($postObj->return_msg);
        }
        if ($postObj->result_code != 'SUCCESS') {
            die($postObj->err_code);
        }
        $id = $postObj->attach;
        $order = Order::find()->where('order_id=:order_id', 
                [
                        ':order_id' => $id
                ])->one();
        $order->order_status = 1;
        
        $order_action = new OrderAction();
        $order_action->order_id = $order->order_id;
        $order_action->log_time = time();
        $order_action->status_desc = "付款成功";
        
        if (! ($order->save() && $order_action->save())) {
            return $this->jsonFail([], '保存失败');
        } else {
            return $this->jsonSuccess([
                    $order
            ], '付款成功,等待发货');
        }
        // $model = Article::find()->where(['id'=>$id])->one();
        // $model ->top_expiry_time=(time()+60*60*24*7);
        // return $model->save(false)?SUCCESS:OK;
        // $postStr = file_get_contents('php://input');
        // file_put_contents('/tmp/123.txt',$postStr);
        // $postObj = simplexml_load_string($postStr, 'SimpleXMLElement',
    // LIBXML_NOCDATA);
        // $id =$postObj ->attach;
        // $model1 = Article::find()->where(['id'=>6])->one();
        // $model1 ->updated_at=1;
        // $model1 ->save();
        // $model = Article::find()->where(['id'=>$id])->one();
        // $model ->updated_at=(time()+60*60*24*7);
        // return $model ->save()?SUCCESS:OK;
        // $postStr = file_get_contents('php://input');
        // $postObj = simplexml_load_string($postStr, 'SimpleXMLElement',
    // LIBXML_NOCDATA);
        //
        // if ($postObj === false) {
        // die('parse xml error');
        // }
        // if ($postObj->return_code != 'SUCCESS') {
        // die($postObj->return_msg);
        // }
        // if ($postObj->result_code != 'SUCCESS') {
        // die($postObj->err_code);
        // }
        //
        // //更新订单信息
        // $openid=$postObj->openid;
        // $out_trade_no=$postObj->out_trade_no;
        // $order = Order::find()->where(['openid'=>$openid,
    // 'out_trade_no'=>$out_trade_no])->one();
        //
        // if($order){
        // $order->status=2;
        // $order->out_trade_no=$out_trade_no;
        // $data = $order->update();
        // }
        //
        // //添加一条VIP记录
        // $user_check = Member::find()->where(['openid'=>$openid ])->one();
        // if($user_check){
        // $user_check->vip= '1';
        // $user_check->created_at=time();
        // $user_check->update();
        // $userid=tools::getuserid($openid);
        // $this->actionAddactivity($userid);
        // }else{
        // $vipmem = new Member();
        // $vipmem->openid = $openid;
        // $vipmem->vip='1';
        // $vipmem->created_at=time();
        // $vipmem->save();
        // $vipid=$vipmem->attributes['id']; //vip的id
        // $userid=tools::getuserid($openid);
        // $this->actionAddactivity($userid);
        // }
        // return
    // '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

    // 添加推广记录
    /*
     * $userid 当前用户iduserid
     * $ancestry 父id
     */
    public function actionAddactivity ($userid)
    {
        $ancestry = TreeController::actionChild($userid);
        $result = TreeController::actioinGetparent($userid, $ancestry);
    }
}