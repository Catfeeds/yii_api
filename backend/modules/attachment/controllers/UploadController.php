<?php

namespace backend\modules\attachment\controllers;

use Yii;
use backend\base\BaseController;
use yii\web\UploadedFile;
use backend\modules\attachment\models\Attachment;
use backend\modules\attachment\models\UploadImage;

class UploadController extends BaseController {
	// 图片上传
	public function actionUploadimage() {
		$user_id = $this->getUserId ();
		$data = Yii::$app->request->post ();
		if (empty ( $user_id ) || empty ( $data ['site_id'] )) {
			return $this->jsonFail ( [ ], '参数缺失' );
		}
		if (empty ( $data ['catid'] )) {
			$data ['catid'] = 0;
		}
		$model = new UploadImage ();
		$model->file = UploadedFile::getInstanceByName ( 'file' );
		$filename = $model->file->name;
		$name = Yii::$app->security->generateRandomString () . "." . $model->file->extension;
		if (! empty ( $model->file ) && $model->validate ()) {
			$url = 'upload/' . date ( 'Y' ) . '/' . date ( 'md' );
			$urls = Yii::$app->params ['upload'] . $url;
			UploadImage::fileExists ( $urls );
			if ($model->file->saveAs ( $urls . '/' . $name )) {
			} else {
				return $this->jsonFail ( $model->file->error, '上传失败' );
			}
		} else {
			return $this->jsonFail ( $model->file, '上传失败,文件格式错误' );
		}
		
		$attachement = new Attachment ();
		$attachement->module = Attachment::STATUS_MODULE;
		$attachement->catid = $data ['catid'];
		$attachement->filename = $filename;
		$attachement->filepath = $url . '/' . $name;
		$attachement->filesize = $model->file->size;
		$attachement->fileext = $model->file->type;
		$attachement->isimage = Attachment::STATUS_IS_IMAGE;
		$attachement->uploadtime = time ();
		$attachement->uploadip = Yii::$app->request->userIP;
		$attachement->authcode = md5 ( $url );
		$attachement->site_id = $data ['site_id'];
		$attachement->status = Attachment::STATUS_IS_SHOW;
		
		if ($attachement->save ()) {
			return $this->jsonSuccess ( $attachement, '上传成功' );
		} else {
			return $this->jsonFail ( $attachement->errors, '上传最终失败' );
		}
	}
	// 外部图片上传
	public function actionUploadtheimage() {
		$user_id = $this->getUserId ();
		$data = Yii::$app->request->post ();
		if (empty ( $user_id )) {
			return $this->jsonFail ( [ ], '参数缺失' );
		}
		if (empty ( $data ['catid'] )) {
			$data ['catid'] = 0;
		}
		$model = new UploadImage ();
		$model->file = UploadedFile::getInstanceByName ( 'file' );
		$filename = $model->file->name;
		$name = Yii::$app->security->generateRandomString () . "." . $model->file->extension;
		if (! empty ( $model->file )) {
			$url = 'upload/' . date ( 'Y' ) . '/' . date ( 'md' );
			$urls = Yii::$app->params ['upload'] . $url;
			UploadImage::fileExists ( $urls );
			if ($model->file->saveAs ( $urls . '/' . $name )) {
			} else {
				return $this->jsonFail ( $model->file->error, '上传失败' );
			}
		} else {
			return $this->jsonFail ( $model->file, '上传失败,文件格式错误' );
		}
		
		$attachement = new Attachment ();
		$attachement->module = Attachment::STATUS_MODULE;
		$attachement->catid = $data ['catid'];
		$attachement->filename = $filename;
		$attachement->filepath = $url . '/' . $name;
		$attachement->filesize = $model->file->size;
		$attachement->fileext = $model->file->type;
		$attachement->isimage = Attachment::STATUS_IS_IMAGE;
		$attachement->uploadtime = time ();
		$attachement->uploadip = Yii::$app->request->userIP;
		$attachement->authcode = md5 ( $url );
		$attachement->site_id = $data ['site_id'];
		$attachement->status = Attachment::STATUS_IS_SHOW;
		
		if ($attachement->save ()) {
			return $this->jsonSuccess ( $attachement, '上传成功' );
		} else {
			return $this->jsonFail ( $attachement->errors, '上传最终失败' );
		}
	}
	// 图片上传百度编辑器
	public function actionUploadimageforueditor() {
		$data = Yii::$app->request->post ();
		$user_id = 0;
		$model = new UploadImage ();
		$model->file = UploadedFile::getInstanceByName ( 'upfile' );
		if (! empty ( $model->file )) {
			$filename = $model->file->name;
			$name = Yii::$app->security->generateRandomString () . "." . $model->file->extension;
			if (! empty ( $model->file ) && $model->validate ()) {
				$url = 'upload/' . date ( 'Y' ) . '/' . date ( 'md' );
				$urls = Yii::$app->params ['upload'] . $url;
				UploadImage::fileExists ( $urls );
				if ($model->file->saveAs ( $urls . '/' . $name )) {
				} else {
					return $this->jsonFail ( $model->file->error, '上传失败' );
				}
			} else {
				return $this->jsonFail ( $model->file, '上传失败,文件格式错误' );
			}
			
			$attachement = new Attachment ();
			$attachement->module = Attachment::STATUS_MODULE;
			$attachement->catid = 0;
			$attachement->filename = $filename;
			$attachement->filepath = $url . '/' . $name;
			$attachement->filesize = $model->file->size;
			$attachement->fileext = $model->file->type;
			$attachement->isimage = Attachment::STATUS_IS_IMAGE;
			$attachement->uploadtime = time ();
			$attachement->uploadip = Yii::$app->request->userIP;
			$attachement->authcode = md5 ( $url );
			$attachement->site_id = 0;
			$attachement->status = Attachment::STATUS_IS_SHOW;
			
			if ($attachement->save ()) {
				$res = array (
						"state" => "SUCCESS", // 上传状态，上传成功时必须返回"SUCCESS"
						"url" => "/" . $attachement->filepath,
						"title" => $filename, // 新文件名
						"original" => $filename, // 原始文件名
						"type" => '.' . $model->file->extension, // 文件类型
						"size" => $attachement->filesize  // 文件大小
				);
				return json_encode ( $res );
			} else {
				return $this->jsonFail ( $attachement->errors, '上传最终失败' );
			}
		} else {
			return $this->jsonFail ( '', '上传失败' );
		}
	}
}