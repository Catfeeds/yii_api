<?php

namespace api\modules\v1\service;

use backend\modules\ucenter\models\User;
use backend\modules\ucenter\models\WeixinUser;

class UserService
{
    //添加用户
    public function create($data,$openid)
    {
        $user = new User();
        $user->status = '1';
        $user->nickname = $data['nickName'];
        $user->avater = $data['avatarUrl'];
        $user_id = $user -> save();
        $wxuser = new WeixinUser();
        $wxuser -> openid = $openid;
        $wxuser -> uid = $user->attributes['id'];
        $wxuser -> create_time = time();
        $wxuser -> headimgurl = $data['avatarUrl'];
        $wxuser -> nickname = $data['nickName'];
        $status = $wxuser->save(false);
        return $status ? $user->attributes['id'] : $wxuser->getErrors();
    }
    public static function getUser($openid)
    {
        return $user = WeixinUser::findByOpenid($openid);
    }
}


