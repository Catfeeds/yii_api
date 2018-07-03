<?php
namespace backend\modules\wechat\service;

use backend\modules\wechat\models\WeixinMessage;
use yii\base\Action;
use backend\modules\wechat\models\MaterialTemporary;
use backend\modules\wechat\models\MaterialText;
use common\extensions\Wechat\WechatReceive;
use common\extensions\Wechat\WechatMedia;

/**
 * 微信群发消息
 */
class MessageService
{
    public static function checkType($type)
    {
        $array = ['mpnews','text','voice','music','image','video','wxcard'];
        if (in_array($type, $array)) {
            return true;
        }else{
            return false;
        }
    }
    public static function Create($site_id,$type,$media_id='',$content='')
    {
        $message = new WeixinMessage();
        $message->site_id = $site_id;
        $message->CreateTime = time();
        $message->MediaId = $media_id;
        $message->MsgType = $type;
        $message->Content = $content;
        return $message->save();
    }
    
    public static function Send($site_id,$id)
    {
        $upload = new WechatMedia($site_id);
        $model = WeixinMessage::find()->where(['site_id' => $site_id])->andWhere(['id' => $id])->one();
        if(empty($model)){
            return '未查询到此消息';
        }
        $array = [
            'filter'=>['is_to_all'=>true,'tag_id'=>'']
        ];
        if($model->type == 'text'){
            if(empty($model->Content)){
                return '无文本内容';
            }
            $array['text']=['content'=>$model->Content];
            $array['msgtype'] = 'text';
        }elseif ($model->type == 'video'){
            /*
             * 基础支持中的上传下载多媒体文件来得到新的media_id
             */
            if(empty($model->MediaId)){
                return '无素材id';
            }
            $upload_data = [
                "media_id"=>$model->MediaId,     //通过上传媒体接口得到的MediaId
                "title"=>$model->Title,    //视频标题
                "description"=>$model->Description//视频描述
            ];
            $msg = $upload->uploadMpVideo($upload_data);
            if($msg){
                $mp_media = $msg['mediaid'];
                $array['mpvideo']=['media_id'=>$model->MediaId];
                $array['msgtype'] = 'mpvideo';
            }else{
                return '视频上传出错!';
            }
        }else{
            if(empty($model->MediaId)){
                return '无素材id';
            }
            $array[$model->type]=['media_id'=>$model->MediaId];
            $array['msgtype'] = $model->type;
        }
        
        $wechat = new WechatReceive($site_id);
        $data = $wechat->sendGroupMassMessage($array);
        if($data){
            $model->MsgId = $data['msg_id'];
            $model->save();
            return 'SUCCESS';
        }else{
            return '发送失败！';
        }
    }
    
    public static function count($site_id,$type=null)
    {
        return WeixinMessage::find()->where([
            'site_id' => $site_id,
        ])->andFilterWhere(['like','type',$type])->count();
    }

    public static function list($site_id,$type=null,$offset, $limit)
    {
        return WeixinMessage::find()
        ->where(['site_id' => $site_id])
        ->andFilterWhere(['type'=>$type])
        ->orderBy('CreateTime DESC')
        ->offset($offset)
        ->limit($limit)
        ->all();
    }
    public static function view($site_id,$id)
    {
        return WeixinMessage::find()
        ->where(['site_id' => $site_id])
        ->andWhere(['id' => $id])
        ->one();
    }
}