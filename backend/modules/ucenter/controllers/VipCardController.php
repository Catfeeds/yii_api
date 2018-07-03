<?php
namespace backend\modules\ucenter\controllers;

use backend\modules\ucenter\models\CardUser;
use backend\modules\ucenter\models\VipCard;
use backend\modules\ucenter\models\VipCardOrder;
use backend\modules\ucenter\service\VipCardService;
use common\extensions\Wechat\WechatMedia;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\CategoryService;
use common\extensions\Wechat\WechatCard;

/**
 * CategoryController implements the CRUD actions for Category model.
 * 会员卡的增删改查
 */
class VipCardController extends BaseController
{
    //验证
    public function findOpenIdByCardNum($phone, $card_Num)
    {
        $weCard = CardUser::find()->where(['weicardnum' => $card_Num])->one();
        if ($weCard != null) {
            return "这张卡被绑定过了!";
        }

        $phone = CardUser::find()->where(['phone' => $phone])->one();
        if ($phone != null) {
            return "这个手机号下面已经有卡了!";
        }

        return true;

    }


    // 添加会员卡,要先找到用户会员卡关联表里面的open_id
    public function actionAdd()
    {

        // 1储值卡 2折扣 3代金券 4储值卡+折扣
        $request = \Yii::$app->request;
        if ($request->isPost) {


            $request_arr = ['card_name', 'card_type', 'end_time', 'site_id', 'phone', 'weicardnum'];
            $request_NotArr = ['stored_sum', 'cash_sum', 'discount'];

            // 调用Service 的方法
            $data = VipCardService::wl_validate($request_arr, $request_NotArr);

            if ($data == "false") {
                return $this->jsonFail("参数缺失", "参数缺失");
            }


            $info = $this->findOpenIdByCardNum($data['phone'], $data['weicardnum']);

            if ($info != "true") {
                return $this->jsonFail($info);
            }


            $info = VipCardService::addCard($data);


            return $this->jsonFail($info, $info);
        } else {
            return $this->jsonFail("", "此接口是Post");
        }
    }

    // 查询会员卡,有两种形式一种是直接查询所有的会员的卡,第二个是按照类型差早
    public function actionFindOrBy()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $type = $request->post('type');

            $card_type_name = $request->post("card_type_name");
            if ($card_type_name == null) // 没输入就查询所有吧
            {
                $info = VipCard::find()->asArray()->all();

                return $this->jsonEncode(true, $info, "查询成功!");
            } else {

                $id = VipCard::getID($card_type_name);
                $info = VipCard::find()->where([
                    'card_type' => $id
                ])
                    ->asArray()
                    ->all();

                return $this->jsonEncode(true, $info, "查询成功!");
            }
        } else {
            return $this->jsonFail("", "此接口是Post");
        }
    }

    // 删除会员卡
    public function actionDelete()
    {

        $request = Yii::$app->request;
        if ($request->isPost) {


            $request_arr = ['weicardnum'];
            $data = VipCardService::wl_validate($request_arr);
            if ($data == "false") {
                return $this->jsonFail("参数缺失", "参数缺失");
            }
            $card = CardUser::find()->where(['weicardnum' => $data['weicardnum']])->one();


            if ($card == null) {
                return $this->jsonFail("", "呀这张卡不存在的!");
            }

            $json = VipCardService::deleteCard($card);

            return $this->jsonSuccess("", $json);

        } else {
            return $this->jsonFail("", "此接口是Post");
        }
    }

    // 修改会员卡
    public function actionUpdate()
    {
        $request = \Yii::$app->request;
        if ($request->isPost) {

            $request_arr = ['weicardnum', 'card_name', 'end_time', 'card_type', 'site_id'];

            $request_NotArr = ['stored_sum', 'cash_sum', 'discount'];

            // 调用Service 的方法
            $data = VipCardService::wl_validate($request_arr, $request_NotArr);
            if ($data == "false") {
                return $this->jsonFail("参数缺失", "参数缺失");
            }
            $card = CardUser::find()->where(['weicardnum' => $data['weicardnum']])->one();


            if ($card == null) {
                return $this->jsonFail("", "呀这张卡不存在的!");
            }
            // 调用Service 的方法
            $info = VipCardService::updateCard($card, $data);

            return $this->jsonFail($info, $info);
        } else {
            return $this->jsonFail("", "此接口是Post");
        }
    }

    /**
     * 会员卡充值
     * Get方法是用来验证手机号码的
     * Post方法是用来提交充值的
     */
    public function actionStoredSumAdd()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {

            $request_data = VipCardService::wl_validate(['weicardnum', 'stored_sum']);
            if ($request_data == "false") {
                return $this->jsonFail("参数缺失", "参数缺失");
            }
            //关联的
            $card_user = CardUser::find()->where(['weicardnum' => $request_data['weicardnum']])->one();

            //会员卡
            $vip_card = $card_user->card;

            //订单
            $vip_card_order = new VipCardOrder();

            if ($vip_card['card_type'] == VipCard::STORED_SUM) {

                $isok = VipCardService::StoredSumAdd($card_user, $vip_card, $vip_card_order, $request_data);
                if ($isok == "true") {

                    return $this->jsonSuccess("充值成功!");
                } else {
                    $this->jsonFail($isok);
                }

            }
        } else {


            $weicardnum = $request->get('weicardnum');
            if ($weicardnum == null) {
                return $this->jsonFail("缺失参数!");
            }
            $phone = CardUser::find()->where(['weicardnum' => $weicardnum])->select(['phone'])->asArray()->one();
            if ($phone == null) {
                return $this->jsonFail("没有这张卡!");
            }

            $this->jsonSuccess($phone['phone']);


        }


    }


}
