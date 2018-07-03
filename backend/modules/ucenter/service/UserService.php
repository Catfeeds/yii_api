<?php
/**
 * @date        : 2017年8月30日
 * @author      : Jason
 * @copyright   : http://www.ixn100.com/
 * @description : 前台用户服务
 */
namespace backend\modules\ucenter\service;
use yii;
use backend\modules\ucenter\models\User;
use backend\modules\cms\models\Relationship;
use backend\modules\ucenter\models\Weixin_user;

class UserService
{
    //添加用户
    public function create($data)
    {
        $user = new User();
        $user->status = '1';
        $user->nickname = $data['nickname'];
        $user->avater = $data['headimgurl'];
        $user_id = $user -> save();
        $wxuser = new Weixin_user();
        $wxuser -> openid = $data['openid'];
        $wxuser -> uid = $user->attributes['id'];
        $wxuser -> create_time = time();
        $wxuser -> headimgurl = $data['headimgurl'];
        $wxuser -> nickname = $data['nickname'];
        $status = $wxuser->save(false);
        return $status ? $user->attributes['id'] : $wxuser->getErrors();
    }
    
    
    //用户关系常量为3关系为本人
    const Relation = 3;
    //修改用户的信息
    public static function update($user_id , $userinfo)
    {
         $model = User::findIdentity($user_id);
         $model -> mobile = $userinfo['mobile'];
         $model -> save();
    }

    //获取用户详情
    public function View($id)
    {
        $model = new User();
        $data = $model::find()->where(['id' => $id])->with(['user'])->asArray()->one();
        return $data;
    }
    //记录用户登录时间
    public function logintime($id){
        $user = User::findOne($id);
        $user->last_login_time = time(); //修改登录时间为当前时间
        $user->save();   //保存
    }
    
    //获取用户关系
    public function UserRelation($user_id,$uid){
        $relation = Relationship::find()->where(['user_id'=>$user_id,'uid'=>$uid])->select(['relation'])->asArray()->one();
        if($user_id==$uid){
            $relation['relation'] = UserService::Relation;
        }
        return $relation['relation'];
    }
    //关注功能$user_id当前用户,$uid被关注用户-- 1代表关注成功，-1代表取消关注成功，
    public function attention($user_id,$uid){
        $relation = Relationship::find()->where(['user_id'=>$user_id,'uid'=>$uid])->select(['relation'])->asArray()->one();
        $relationship = new Relationship();
        //关注
        if(empty($relation)){
            $data = array(array($user_id,$uid,0),array($uid,$user_id,2));
            Yii::$app->db->createCommand()->batchInsert(Relationship::tableName(), ['user_id','uid','relation'], $data)->execute();
            User::updateAllCounters(['notice_num' => 1], ['id' => $user_id]);
            User::updateAllCounters(['fans_num' => 1], ['id' => $uid]);
            return 1;
        }//取消关注
        else if((int)($relation['relation'])== 0){
            Relationship::deleteAll(['or',['user_id'=>$user_id,'uid'=>$uid],['user_id'=>$uid,'uid'=>$user_id]]);
            User::updateAllCounters(['notice_num' => -1], ['id' => $user_id]);
            User::updateAllCounters(['fans_num' => -1], ['id' => $uid]);
            return -1;
        }//互相关注
        else if((int)($relation['relation'])== 2){
            $relationship1 = Relationship::find()->where(['user_id' => $user_id,'uid'=>$uid])->one();
            $relationship2 = Relationship::find()->where(['user_id' =>$uid ,'uid'=>$user_id])->one();
            $relationship1->relation = 1;
            $relationship2->relation = 1;
            $relationship1->save();
            $relationship2->save();
            User::updateAllCounters(['notice_num' => 1], ['id' => $user_id]);
            User::updateAllCounters(['fans_num' => 1], ['id' => $uid]);
            return 1;
        }//单方面取消关注
        else{
            $relationship1 = Relationship::find()->where(['user_id' => $user_id,'uid'=>$uid])->one();
            $relationship2 = Relationship::find()->where(['user_id' =>$uid ,'uid'=>$user_id])->one();
            $relationship1->relation = 2;
            $relationship2->relation = 0;
            $relationship1->save();
            $relationship2->save();
            User::updateAllCounters(['notice_num' => -1], ['id' => $user_id]);
            User::updateAllCounters(['fans_num' => 1], ['id' => $uid]);
            return -1;
        }
    }
    
    //1、正常 2、禁言1天 3、禁言7天  4:黑名单 
    //处罚功能
    public function punish($id){
        //当前用户对象
        $model = User::findOne($id);
        //禁言
        if($model['status']>1 && $model['status']<4 && $model['expires_in'] == 0){
            $expires_in=($model['status']==2?strtotime("+1 day"):strtotime("+7 day"));
            $model->status=$model['status'];
            $model->expires_in=$expires_in;
            $model->save(false);
        }
        //封号一周
        if($model['status']==4 && $model['expires_in'] == 0){
            $expires_in=(time()+(60*60*24*7));
            $model->status=$model['status'];
            $model->expires_in=$expires_in;
            $model->save(false);
        }
        //判断状态是否过期如果过期将状态改为正常
        if($model['status']>1 && $model['status']<5 && $model['expires_in']>0  && $model['expires_in']<time()){
            $model->status=1;
            $model->expires_in=0;
            $model->save(false);
        }
    }
    //获取当前用户状态
    public function userstate($id){
        $userstate = User::find()->select(['status','expires_in'])
            ->where(['id' => $id])->asArray()->one();
        return $userstate;
    }


}


