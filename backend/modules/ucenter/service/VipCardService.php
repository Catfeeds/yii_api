<?php
/**
 * @date        : 2018年1月29日
 *
 *
 */
namespace backend\modules\ucenter\service;

use backend\modules\restaurant\models\Tables;
use backend\modules\attachment\service\QrService;
use backend\modules\ucenter\models\CardUser;
use backend\modules\ucenter\models\VipCard;
use backend\modules\ucenter\models\WeiCard;
use common\extensions\Wechat\WechatCard;
use Yii;

class VipCardService
{
    public static function dd($arr)
    {
        echo "<pre>";
        var_dump($arr);
    }

    //这是一个验证加返回数据的方法,第一个参数是必须的参数,第二个是不是必须的,最后都合成一个数组
    public static function wl_validate($must, $notmust = null)
    {
        $request = \Yii::$app->request;
        $data = [];
        if ($notmust != null) {
            foreach ($notmust as $item) {
                if ($item != null) {
                    $data[$item] = $request->post($item);
                }

            }
        }
        foreach ($must as $item) {
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

    //添加会员卡的

    public static function addCard($data)
    {

        $vipCard = new VipCard();
        $vipCard->card_name = $data['card_name'];
        $vipCard->end_time = $data['end_time'];
        $vipCard->card_type = $data['card_type'];
        $vipCard->stored_sum = $data['stored_sum'];
        $vipCard->cash_sum = $data['cash_sum'];
        $vipCard->discount = $data['discount'];
        $vipCard->site_id = $data['site_id'];

        $tr = Yii::$app->db->beginTransaction();

        try {
            if ($vipCard->validate()) {
                $vipCard->save();

                $id = $vipCard->attributes['id'];

                $weCard = new CardUser();

                $weCard->weicardnum = $data['weicardnum'];

                $weCard->vipcardid = $id;

                $weCard->phone = $data['phone'];

                $weCard->site_id = $data['site_id'];

                $weCard->save();
                $tr->commit();                 //都保存成功了,事务提交
                return "添加成功!";
            } else {
                return "缺少参数,验证失败!";
            }

        } catch (\yii\db\Exception $e) {

            $tr->rollBack();   //回滚
//                echo "出错了,小小的回滚一波";
            return "添加失败了";
        }

    }

    //修改的跟添加的没有区别
    public static function updateCard($card, $data)
    {

        $vipCard = $card->getCard()->one();

        if ($vipCard == null) {
            return "这张卡还没有开卡!";
        };
        $vipCard->card_name = $data['card_name'];
        $vipCard->end_time = $data['end_time'];
        $vipCard->card_type = $data['card_type'];
        $vipCard->stored_sum = $data['stored_sum'];
        $vipCard->cash_sum = $data['cash_sum'];
        $vipCard->discount = $data['discount'];
        $vipCard->site_id = $data['site_id'];

        //开启一个事务

        $tr = Yii::$app->db->beginTransaction();

        try {
            if ($vipCard->validate()) {
                $vipCard->save();
                $tr->commit();                 //都保存成功了,事务提交
                return "修改成功!";
            } else {
                return "缺少参数,验证失败!";

            }

        } catch (\yii\db\Exception $e) {

            $tr->rollBack();   //回滚

            return "修改失败了";
        }

    }

//删除的
    public static function deleteCard($card)
    {
        $vipCard = $card->getCard();

        if ($vipCard->one() == null) {
            return "这张卡还没有开卡!";
        };

        //开启一个事务

        $tr = Yii::$app->db->beginTransaction();

        try {

            $vipCard->one()->delete();
            $card->vipcardid = -1;
            $card->weicardnum = -1;
            $card->save();
            $tr->commit();                 //都保存成功了,事务提交
            return "删除成功!";


        } catch (\yii\db\Exception $e) {

            $tr->rollBack();   //回滚

            return "修改失败了";
        }

    }

    //添加微信的优惠券
    public static function WeCouponAdd($card_data)
    {

        $data = "";
        if ($card_data['card_type'] == "cash") {

            $data = [
                'card' => [
                    'card_type' => VipCard::WE_CASH,
                    'cash' => [
                        'base_info' => [
                            'logo_url' => 'http://p7h441d6x.bkt.clouddn.com/xn.jpg',
                            "code_type" => "CODE_TYPE_ONLY_QRCODE",
                            "brand_name" => $card_data['brand_name'],
                            "title" => $card_data['title'],
                            "color" => "Color010",
                            "notice" => $card_data['notice'],
                            "service_phone" => "020-88888888",
                            "description" => $card_data['description'],

                            "date_info" => [
                                "type" => "DATE_TYPE_FIX_TERM",
                                "fixed_term" => 15,
                                "fixed_begin_term" => 0,
                                "end_timestamp" => 1535854938
                            ],
                            "sku" => [
                                "quantity" => $card_data['quantity']
                            ],

                        ],
                        "least_cost" => $card_data['least_cost'],
                        "reduce_cost" => $card_data['reduce_cost']
                    ]
                ]
            ];
        } else if ($card_data['card_type'] == "discount") {
            $data = [
                'card' => [
                    'card_type' => VipCard::WE_DISCOUNT,
                    'discount' => [
                        'base_info' => [
                            'logo_url' => 'http://p7h441d6x.bkt.clouddn.com/xn.jpg',
                            "code_type" => "CODE_TYPE_ONLY_QRCODE",
                            "brand_name" => $card_data['brand_name'],
                            "title" => $card_data['title'],
                            "color" => "Color010",
                            "notice" => $card_data['notice'],
                            "service_phone" => "020-88888888",
                            "description" => $card_data['description'],

                            "date_info" => [
                                "type" => "DATE_TYPE_FIX_TERM",
                                "fixed_term" => 15,
                                "fixed_begin_term" => 0,
                                "end_timestamp" => 1535854938
                            ],
                            "sku" => [
                                "quantity" => $card_data['quantity']
                            ],

                        ],
                        "discount" => $card_data['discount']

                    ]
                ]
            ];

        } else {

            return "创建卡错误!";
        }


        $card = new WechatCard($card_data['site_id']);
        $isok = $card->createCard($data);

        return $isok;

    }


    /**
     * 添加微信的会员卡
     * @param $brand_name
     * @param $title
     * @param $notice
     * @param $description
     * @param $quantity
     * @param $prerogative
     * @param $name
     * @param $tips
     * @param $url
     *$brand_name='小牛餐饮',$title='会员卡',$notice='请出示二维码',
     * $description='不可与其他优惠同享' ,$quantity='1',$prerogative='这是注意事项',
     * $name='店铺',$tips='店铺详情',$url='http://m.xngw.ixn123.com/#/home'
     */
    public static function WeCardAdd($card_data)
    {
        $data = [
            'card' => [
                'card_type' => VipCard::WE_Card,
                'member_card' => [
                    'base_info' => [
                        'logo_url' => 'http://p7h441d6x.bkt.clouddn.com/xn.jpg',
                        "code_type" => "CODE_TYPE_ONLY_QRCODE",
                        "brand_name" => $card_data['brand_name'],
                        "title" => $card_data['title'],
                        "color" => "Color010",
                        "notice" => $card_data['notice'],
                        "service_phone" => "020-88888888",
                        "description" => $card_data['description'],

                        "date_info" => [
                            "type" => "DATE_TYPE_FIX_TERM",
                            "fixed_term" => 15,
                            "fixed_begin_term" => 0,
                            "end_timestamp" => 1535854938
                        ],
                        "sku" => [
                            "quantity" => $card_data['quantity']
                        ],
                        "get_limit" => 1,
                        "can_give_friend" => true

                    ],
                    "supply_bonus" => false,
                    "supply_balance" => false,
                    "prerogative" => $card_data['prerogative'],
                    "auto_activate" => true,
                    "custom_cell1" => [
                        "name" => $card_data['name'],
                        "tips" => $card_data['tips'],
                        "url" => $card_data['url'],
                    ],


                ]
            ]
        ];
        $tr = Yii::$app->db->beginTransaction();

        try {
            $card = new WechatCard($card_data['site_id']);
            $isok = $card->createCard($data);


            $weiCard = new WeiCard();
            $weiCard->brand_name = $card_data['brand_name'];
            $weiCard->title = $card_data['title'];
            $weiCard->notice = $card_data['notice'];
            $weiCard->description = $card_data['description'];
            $weiCard->quantity = $card_data['quantity'];
            $weiCard->prerogative = $card_data['prerogative'];
            $weiCard->name = $card_data['name'];
            $weiCard->tips = $card_data['tips'];
            $weiCard->url = $card_data['url'];
            $weiCard->site_id = $card_data['site_id'];
            $weiCard->card_id = $isok['card_id'];

            $weiCard->save();
            $tr->commit();

        } catch (\yii\db\Exception $e) {

            $tr->rollBack();   //回滚

            return "添加失败了";
        }
        return $isok;
    }

    public static function StoredSumAdd($card_user, $vip_card, $vip_card_order, $request_data)
    {
        //修改会员的金额,订单表添加数据
        $tr = Yii::$app->db->beginTransaction();
        try {

            $vip_card->stored_sum = $vip_card->stored_sum + $request_data['stored_sum'];

            $card_user = $card_user->toArray();
            $vip_card_order->phone = $card_user['phone'];
            $vip_card_order->weicardnum = $card_user['weicardnum'];
            $vip_card_order->money = $request_data['stored_sum'];

            $order_num = time() . $card_user['weicardnum'] . rand(1000, 9999);

            $vip_card_order->order_num = $order_num;

            if ($vip_card->validate() == false) {
                return "参数验证失败!";
            }

            if ($vip_card_order->validate() == false) {
                return "参数验证失败!";
            }

            $vip_card->save();
            $vip_card_order->save();

            $tr->commit();
            return "true";


        } catch (\yii\db\Exception $exception) {
            $tr->rollBack();
            return "false";
        }
    }

}
