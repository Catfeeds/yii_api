<?php
// 图片处理
namespace backend\modules\attachment\controllers;
use Yii;
use backend\base\BaseController;
use backend\modules\attachment\models\Attachment;
use yii\imagine\Image;
use backend\modules\admin\models\Site;
use backend\modules\attachment\service\QrService;

class ImageController extends BaseController
{
    public function actionImage ()
    {
        $url = Yii::$app->request->get('url');
        if(empty($url)){
            return null;
        }
        if (empty($imageinfo = getimagesize(Yii::$app->params['upload'] . $url))) {
            return $this->jsonFail($url, '非图片文件');
        } else {
            $mime = $imageinfo['mime'];
        }
        $process = Yii::$app->request->get('oss-process');
        $process_a = explode(',', $process);
        $data = array();
        if (! empty($process)) {
            foreach ($process_a as $i) {
                $d = explode('_', $i);
                $data[$d[0]] = $d[1];
            }
        }
        $filename = strrchr($url, '/');
        header('Cache-control: max-age=3600');
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600) . ' GMT');
        $formats = strrchr($url, '.'); // 文件格式获取
        header('Content-Type:' . $mime);
        header('Content-Transfer-Encoding: binary');
        
        if (empty($data['h']) || empty($data['w'])) {
            readfile(Yii::$app->params['upload'] . $url);
        }
        $heigth = $data['h'];
        $weight = $data['w'];
        if (! empty($heigth) && ! empty($weight)) {
            $aimImg = Yii::$app->params['upload'] .
                     substr($url, 0, - 1 * strlen($formats)) . '_' . $heigth .
                     'X' . $weight . $formats;
            $url = Yii::$app->params['upload'] . $url;
            if (is_file($aimImg)) {
                readfile($aimImg);
            }
            Image::thumbnail($url, $weight, $heigth, 'outbound')->save($aimImg, 
                    [
                            'quality' => 100
                    ]);
        } else {
            return $this->jsonFail([], '处理失败');
        }
        readfile($aimImg);
    }
    
    public function actionCreatqr()
    {
        $table_name = Yii::$app->request->post('table_name');
        $site_id = Yii::$app->request->post('site_id');
        if(empty($table_name)||empty($site_id)){
            return $this->jsonFail([], '参数缺失');
        }
        $site_name = Site::findOne(['site_id'=>$site_id])->name;
        if(empty($site_name)){
            return $this->jsonFail([], '没有此店铺名称');
        }
        $our = '小牛餐饮提供技术支持';
        $text = [$table_name,$our,$site_name];
        $background = 'http://api.xn.demo-xn.itdongli.com/background.png';
        
        $QR = 'http://api.xn.demo-xn.itdongli.com/Qr.png';
        
        $url= Yii::$app->params['upload']."Qr/".$site_name;
        if(!file_exists($url)){
            mkdir($url,0777,true);
        }
        $url .= "/".$table_name.".png";
        Image::watermark($background,$QR, [120, 112])->save($url, ['quality' => 100]);
        
        $textOpt = [['color'=>'fff','size'=>'35'],['color'=>'A4DE83','size'=>'11'],['color'=>'000','size'=>'20']];
        $ttf = 'simhei.ttf';
        foreach ($textOpt as $i => $textO){
            $textwidth = imagettfbbox($textO['size'],0,$ttf,$text[$i]);
            $x[$i] = (680 - $textwidth[2]+$textwidth[0]) / 2;
        }
        Image::text($url, $table_name, $ttf, [$x[0], 40], $textOpt[0])->save($url, ['quality' => 100]);
        Image::text($url, $our, $ttf, [$x[1], 643], $textOpt[1])->save($url, ['quality' => 100]);
        Image::text($url, $site_name, $ttf, [$x[2], 725], $textOpt[2])->save($url, ['quality' => 100]);
        
        header('Cache-control: max-age=3600');
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600) . ' GMT');
        $formats = strrchr($url, '.'); // 文件格式获取
        header('Content-Type: image/png');
        header('Content-Transfer-Encoding: binary');
        readfile($url);
    }
}
