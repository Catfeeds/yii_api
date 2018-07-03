<?php
/**
 * @date        : 2017年9月24日
 * @author      : sun
 * @copyright   : http://www.hoge.cn/
 * @description :
 */
namespace backend\modules\wechat\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFiles;
    public $file;

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg,png,gif,jpeg,pem','maxFiles' => 5],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pem'],
        ];
    }
    public function upload()
    {
        if ($this->validate()) {
            $path = "../../data/uploads/".date("Ymd");
            if (!is_dir($path)){
                mkdir($path);
            }
            foreach ($this->imageFiles as $file) {
                $fileName = date("HiiHsHis").$file->baseName . "." . $file->extension;
                $dir = $path."/". $fileName;
                $file->saveAs($dir);
                $uploadSuccessPath[] = "/uploads/".date("Ymd")."/".$fileName;
                unset($fileName,$dir);
            }
            $result = [
                'uploadSuccessPath' => $uploadSuccessPath,
            ];
            return $result;
        } else {
            return $this->getErrors();
        }
    }
    
    //创建上传路径
    public static function fileExists($uploadpath){
        {
            if(!file_exists($uploadpath)){
                mkdir($uploadpath,0777,true);
            }
            return $uploadpath;
        }
    }

}