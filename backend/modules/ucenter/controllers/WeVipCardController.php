<?php
namespace backend\modules\ucenter\controllers;

use backend\modules\ucenter\models\CardUser;
use backend\modules\ucenter\models\VipCard;
use backend\modules\ucenter\service\VipCardService;
use common\extensions\Wechat\WechatMedia;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\CategoryService;
use common\extensions\Wechat\WechatCard;

/**
 * CategoryController implements the CRUD actions for Category model.
 * 微信会员卡的控制器
 */
class WeVipCardController extends BaseController
{
    //展示优惠券的二维码
    public function actionShowCard()
    {
        $request = Yii::$app->request;

        $we_card_id = $request->get('we_card_id');

        $w_card = new WechatCard("2");

        $info = $w_card->createCardQrcode($we_card_id);

        $qr_code = $info["show_qrcode_url"];

        print_r($qr_code);

        echo "<img src='$qr_code'>";

    }

    /* 创建微信的优惠券
     * 只有两种卡,一种带金券(cash),一种折扣券(discount)
     *
     * @brand_name  商家名称
     * @title  券的标题
     * @notice           卡券使用提醒，字数上限为16个汉字。
     * @description    是在卡券的详情中显示的 提示
     * @quantity      就是库存多少张卡
     *
     * @discount    这个就是折扣
     *
     * @least_cost    代金券的门槛金额
     * @reduce_cost    减免的金额
     */
    public function actionWeCoupon()
    {
        $request = \Yii::$app->request;

        if ($request->isPost) {
            $validate_arr = ['card_type', 'brand_name', 'title', 'notice', 'description', 'quantity', 'site_id'];
            $card_data = $this->wl_validate($validate_arr);
            if ($card_data == "false") {
                return $this->jsonFail("同志缺少参数呀1!");
            }

            if ($card_data['card_type'] == "cash") {
                if ($request->post('least_cost') == null || $request->post('reduce_cost') == null) {
                    return $this->jsonFail("同志缺少参数呀2!");
                } else {
                    $card_data['least_cost'] = $request->post('least_cost');
                    $card_data['reduce_cost'] = $request->post('reduce_cost');
                }
            }

            if ($card_data['card_type'] == "discount") {
                if ($request->post('discount') == null) {
                    return $this->jsonFail("同志缺少参数呀3!");
                } else {
                    $card_data['discount'] = $request->post('discount');
                }
            }

            $isok = VipCardService::WeCouponAdd($card_data);

            // 处理执行结果
            if ($isok === FALSE) {
                // 接口失败的处理
                echo $isok->errMsg;
            } else {
                $this->jsonSuccess($isok);
            }
        }


    }

    /**
     * 创建微信的会员卡
     * @brand_name  商家名称
     * @title  会员卡的标题
     * @notice           卡券使用提醒，字数上限为16个汉字。
     * @description    会员卡详情中显示的 得使用须知
     * @prerogative   会员卡详情得特权说明
     * @quantity      就是库存多少张卡
     * 然后还有一个必填得URL
     * @name   URL的名字
     * @tips   URL右边还有一段话
     * @url    URL
     */

    public function actionWeCard()
    {

        $request = \Yii::$app->request;

        if ($request->isPost) {


            $arr = ['brand_name', 'title', 'notice', 'description', 'quantity', 'prerogative', 'name', 'tips', 'url', 'site_id'];

            $data = $this->wl_validate($arr);

            if ($data == "false") {
                return $this->jsonFail("同志缺少参数呀!");
            }

            $isok = VipCardService::WeCardAdd($data);

            // 处理执行结果
            if ($isok === FALSE) {
                // 接口失败的处理
                echo $isok->errMsg;
            } else {
                $this->jsonSuccess($isok);

            }
        }
    }


    /**
     * 一个多参数的自定义的验证方法,验证成功会返回一个数组的数据
     * @param $arr
     * @return array|string
     */
    public function wl_validate($arr)
    {
        $request = \Yii::$app->request;
        $data = [];
        foreach ($arr as $item) {
            if ($item != null) {
                if ($request->post($item) == null) {

                    return "false";
                } else {

                    $data[$item] = $request->post($item);
                }
            }

        }

        return $data;

    }

}
