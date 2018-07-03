<?php

namespace backend\modules\attachment\models;

use Yii;

/**
 * This is the model class for table "pre_attachment".
 *
 * @property integer $aid
 * @property string $module
 * @property integer $catid
 * @property string $filename
 * @property string $filepath
 * @property integer $filesize
 * @property string $fileext
 * @property integer $isimage
 * @property integer $isthumb
 * @property integer $downloads
 * @property integer $userid
 * @property integer $uploadtime
 * @property string $uploadip
 * @property integer $status
 * @property string $authcode
 * @property integer $site_id
 */
class Attachment extends \yii\db\ActiveRecord
{
	const STATUS_MODULE= 'content';
	const STATUS_IS_IMAGE= 1;
	const STATUS_NOT_IMAGE= 0;
	const STATUS_IS_DELETE = 1;
	const STATUS_IS_SHOW = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module', 'filename', 'filepath', 'fileext', 'uploadip', 'authcode'], 'required'],
            [['aid', 'catid', 'filesize', 'isimage', 'isthumb', 'downloads', 'userid', 'uploadtime', 'status', 'site_id'], 'integer'],
            [['module', 'uploadip'], 'string', 'max' => 15],
            [['filename'], 'string', 'max' => 50],
            [['filepath'], 'string', 'max' => 200],
            [['fileext'], 'string', 'max' => 10],
            [['authcode'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'aid' => 'Aid',
            'module' => 'Module',
            'catid' => 'Catid',
            'filename' => 'Filename',
            'filepath' => 'Filepath',
            'filesize' => 'Filesize',
            'fileext' => 'Fileext',
            'isimage' => 'Isimage',
            'isthumb' => 'Isthumb',
            'downloads' => 'Downloads',
            'userid' => 'Userid',
            'uploadtime' => 'Uploadtime',
            'uploadip' => 'Uploadip',
            'status' => 'Status',
            'authcode' => 'Authcode',
            'site_id' => 'site_id',
        ];
    }
}
