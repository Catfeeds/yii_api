<?php

namespace backend\modules\sms\models;

use Yii;

/**
 * This is the model class for table "{{%sms_log}}".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $session_id
 * @property integer $created_at
 * @property string $code
 * @property integer $status
 * @property string $msg
 * @property integer $scene
 * @property string $error_msg
 */
class SmsLog extends \yii\db\ActiveRecord
{
    const STATUS_REGISTER = 1;
    const STATUS_FINDPASSWORD = 2;
    const STATUS_LOGIN = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'status', 'scene'], 'integer'],
            [['error_msg'], 'string'],
        	[['mobile'], 'filter', 'filter' => 'trim'],
        	[['mobile'],'match','pattern'=>'/^1[0-9]{10}$/','message'=>'{attribute}必须为1开头的11位纯数字'],
        	[['mobile'], 'string', 'max' => 11],
            [['session_id'], 'string', 'max' => 128],
            [['code'], 'string', 'max' => 10],
            [['msg'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => 'Mobile',
            'session_id' => 'Session ID',
            'created_at' => 'Created At',
            'code' => 'Code',
            'status' => 'Status',
            'msg' => 'Msg',
            'scene' => 'Scene',
            'error_msg' => 'Error Msg',
        ];
    }
}
