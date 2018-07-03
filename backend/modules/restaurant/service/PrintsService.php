<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\restaurant\service;

use Yii;
use backend\modules\restaurant\models\Prints;
use common\extensions\ylyprint;
use backend\modules\ucenter\models\Admin;
use common\extensions\printcenter;

class PrintsService
{
    public static function showbysite($site_id){
        $models = Prints::showbysite($site_id);
        foreach ($models as $i => $model)
        {
            $models[$i]['print_for'] = array_filter(explode(",",$model['print_for']));
        }
        return $models;
    }
    //添加打印机
    public static function create($data,$user_id)
    {
        $model = new Prints();
        $print_for = '';
        foreach ($data['print_for'] as $v){
            $print_for .= $v.",";
        }
        $data['print_for'] = $print_for;
        if($model->load($data,'')&&$model->validate()){
            $mobilephone = Admin::findOne(['id'=>$user_id])->mobile;
            if($data['brand'] == '1'){
                $ypring = new ylyprint();
                $config = Yii::$app->params['yiliany'];
                $ypring = new ylyprint();
                $config = Yii::$app->params['yiliany'];
                $msg = $ypring->action_addprint($config['user_id'], $model->eq_number, $config['user_name'], $model->name, $mobilephone, $config['api'], $model->eq_key);
                if($msg != 1){
                    return ['msg'=>$msg];
                }
                $ypring->action_print($config['user_id'],$model->eq_number, $data['name'].'\n打印机添加成功', $config['api'], $model->eq_key);
                if($model->save()){
                    return ['msg'=>$msg,'print'=>$model];
                }
            }elseif($data['brand'] == '2'){
                $printcenter = new printcenter();
                $msg = json_decode($printcenter->getStatus($model->eq_number, $model->eq_key),true);
                
                if($msg['responseCode']==0){
                    if($model->save()){
                        $printcenter->sendPrint($model->eq_number, $model->eq_key, '打印机添加成功');
                        return ['msg'=>1,'print'=>$model];
                    }
                }
                return ['msg'=>$msg];
            }
        }
        return false;
    }
    //修改打印机信息
    public static function update($data,$user_id,$site_id){
        $model = Prints::findOne(['id'=>$data['id'],'site_id'=>$site_id]);
        $eq_number = $model->eq_number;
        $eq_key = $model->eq_key;
        if(!empty($data['print_for'])){
            $print_for = '';
            foreach ($data['print_for'] as $v){
                $print_for .= $v.",";
            }
            $data['print_for'] = $print_for;
        }
        if(!empty($model)&&$model->load($data,'')&&$model->validate()){
            //易联云打印机 先删除后添加新打印机
            if($model->brand == 1){
                $mobilephone = Admin::findOne(['id'=>$user_id])->mobile;
                $ypring = new ylyprint();
                $config = Yii::$app->params['yiliany'];
                $msg = $ypring->action_removeprinter($config['user_id'], $model->eq_number, $config['api'], $model->eq_key);
                if($msg != 0){
                    return ['msg'=>$msg];
                }
                $msg = $ypring->action_addprint($config['user_id'], $model->eq_number, $config['user_name'], $model->name, ' ', $config['api'], $model->eq_key);
                if($msg != 0){
                    $ypring->action_addprint($config['user_id'], $eq_number, $config['user_name'], $model->name, ' ', $config['api'], $eq_key);
                    return ['msg'=>2002];
                }
                $ypring->action_print($config['user_id'],$model->eq_number, $data['name'].'\n打印机修改成功', $config['api'], $model->eq_key);
            }elseif($model->brand == 2){
                $printcenter = new printcenter();
                $msg = json_decode($printcenter->getStatus($model->eq_number, $model->eq_key),true);
                $printcenter->sendPrint($model->eq_number, $model->eq_key, '打印机修改成功');
                if($msg['responseCode']==0&&$model->save()){
                    return ['msg'=>1,'print'=>$model];
                }
                
            }
            if($model->save()){
                
                return ['msg'=>$msg,'print'=>$model];        
            }
        }
        return null;   
    }
    //删除打印机
    public static function delete($id,$site_id)
    {
        $model = Prints::findOne(['id'=>$id,'site_id'=>$site_id]);
        if(empty($model)){
            return false;
        }
        if($model->brand == 1){
            $ypring = new ylyprint();
            $config = Yii::$app->params['yiliany'];
            $model = $ypring->action_removeprinter($config['user_id'], $model->eq_number, $config['api'], $model->eq_key);
            if($model!=0){
                return false;
            }
        }elseif($model->brand == 2){
            //不做处理直接删除
        }
        
        return $model->delete();
    }
}
