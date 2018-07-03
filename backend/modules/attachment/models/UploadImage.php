<?php

namespace backend\modules\attachment\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadImage extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        		[['file'], 'file','extensions' => 'jpg ,jpeg, png , gif', 'mimeTypes' => 'image/jpeg , image/png , image/gif'],
        ];
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