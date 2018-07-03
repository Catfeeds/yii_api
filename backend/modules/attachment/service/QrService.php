<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\attachment\service;

use Yii;
use backend\modules\attachment\models\Attachment;
use yii\imagine\Image;
use backend\modules\admin\models\Site;
use backend\modules\restaurant\models\Tables;
use dosamigos\qrcode\QrCode;
use backend\modules\restaurant\service\TablesService;

class QrService
{
    /*
     * 使用phpqrcode生成二维码，
     * 生成可以跳转微信小程序的二维码
     */
    public static function createQR($url = '',$table_id='')
    {
        $errorCorrectionLevel = 'M';    //容错级别
        $matrixPointSize = 15;           //生成图片大小 
        $imageurl = Yii::$app->params['upload'].'Qr/'.time().'png';
        $image = QrCode::png($url.$table_id,$imageurl,$errorCorrectionLevel,$matrixPointSize,1);
        Image::thumbnail($imageurl, 440, 440)->save($imageurl,['quality' => 100]);
        return $imageurl;
    }
    
    public static function gettableQr($id)
    {
        $table = Tables::findOne(['id'=>$id]);
        //无桌号跳出
        if(empty($table)){
            return null;
        }
        //已有二维码直接输出
        if(!empty($table->QR_code)){
            $url = $table->QR_code;
            return $url;
        }
        //无二维码 生成二维码链接
        $table_name = $table->name;
        $site_name = Site::findOne(['site_id'=>$table->site_id])->name;
        if(empty($site_name)){
            return null;
        }
        $our = Yii::$app->params['our'];
        $text = [$table_name,$our,$site_name];
        
        $background = '../web/images/Qr/background.png';//Yii::$app->params['background'];
        $QR = self::createQR(Yii::$app->params['QRurl'],$id);
        //$QR = 'http://api.xn.demo-xn.itdongli.com/Qr.png';
        $url= Yii::$app->params['upload']."Qr/".$text[2];
        
        if(!file_exists($url)){
            mkdir($url,0777,true);
        }
        $url .= "/".$text[0].".png";
        Image::watermark($background,$QR, [120, 112])->save($url, ['quality' => 100]);
        
        $textOpt = [['color'=>'fff','size'=>'35'],['color'=>'A4DE83','size'=>'11'],['color'=>'000','size'=>'20']];
        $ttf = 'simhei.ttf';
        foreach ($textOpt as $i => $textO){
            $textwidth = imagettfbbox($textO['size'],0,$ttf,$text[$i]);
            $x[$i] = (680 - $textwidth[2]+$textwidth[0]) / 2;
        }
        Image::text($url, $text[0], $ttf, [$x[0], 40], $textOpt[0])->save($url, ['quality' => 100]);
        Image::text($url, $text[1], $ttf, [$x[1], 643], $textOpt[1])->save($url, ['quality' => 100]);
        Image::text($url, $text[2], $ttf, [$x[2], 725], $textOpt[2])->save($url, ['quality' => 100]);
        
        $table->QR_code = $url;
        $table->save();
        return $url;
    }
    //多图创建zip
    public static function gettableQrzip($ids)
    {
        $zip = new \ZipArchive();
        $zipname = time().rand(1000,9999);
        if (!file_exists($zipname)){
            $zip->open($zipname.'.zip',\ZipArchive::OVERWRITE|\ZipArchive::CREATE);//创建一个空的zip文件
            foreach ($ids as $i => $id)
            {
                $table = Tables::findOne(['id'=>$id]);
                //无桌号跳出
                if(empty($table)){
                    continue;
                }
                //已有二维码 直接插入zip文件
                if(!empty($table->QR_code)){
                    $url = $table->QR_code;
                    $filename = TablesService::getname($id).'.png';;
                    $zip->addFile($url,$filename); 
                    continue;
                }
                //无二维码 生成二维码链接
                $table_name = $table->name;
                $site_name = Site::findOne(['site_id'=>$table->site_id])->name;
                if(empty($site_name)){
                    return null;
                }
                $our = '小牛餐饮提供技术支持';
                $text = [$table_name,$our,$site_name];
                
                $background = '../web/images/Qr/background.png';
                $QR = self::createQR('https://api.m.demo-xn.itdongli.com/table/',$id);
                //$QR = 'http://api.xn.demo-xn.itdongli.com/Qr.png';
                $url= Yii::$app->params['upload']."Qr/".$text[2];
                
                if(!file_exists($url)){
                    mkdir($url,0777,true);
                }
                $url .= "/".$text[0].".png";
                Image::watermark($background,$QR, [120, 112])->save($url, ['quality' => 100]);
                
                $textOpt = [['color'=>'fff','size'=>'35'],['color'=>'A4DE83','size'=>'11'],['color'=>'000','size'=>'20']];
                $ttf = 'simhei.ttf';
                foreach ($textOpt as $i => $textO){
                    $textwidth = imagettfbbox($textO['size'],0,$ttf,$text[$i]);
                    $x[$i] = (680 - $textwidth[2]+$textwidth[0]) / 2;
                }
                Image::text($url, $text[0], $ttf, [$x[0], 40], $textOpt[0])->save($url, ['quality' => 100]);
                Image::text($url, $text[1], $ttf, [$x[1], 643], $textOpt[1])->save($url, ['quality' => 100]);
                Image::text($url, $text[2], $ttf, [$x[2], 725], $textOpt[2])->save($url, ['quality' => 100]);
                
                $table->QR_code = $url;
                $table->save();
                $filename = TablesService::getname($id).'.png';
                $zip->addFile($url,$filename);
            }
            $zip->close();
            return $zipname.'.zip';
        }else{
            return null;
        }
    }
}


