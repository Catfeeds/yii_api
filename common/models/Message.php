<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pre_message".
 *
 * @property string $messageid
 * @property string $send_from_id
 * @property string $send_to_id
 * @property string $folder
 * @property integer $status
 * @property string $message_time
 * @property string $subject
 * @property string $content
 * @property string $replyid
 * @property integer $type
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pre_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['folder', 'content'], 'required'],
            [['folder', 'content'], 'string'],
            [['status', 'message_time', 'replyid', 'type'], 'integer'],
            [['send_from_id', 'send_to_id'], 'string', 'max' => 30],
            [['subject'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'messageid' => 'Messageid',
            'send_from_id' => 'Send From ID',
            'send_to_id' => 'Send To ID',
            'folder' => 'Folder',
            'status' => 'Status',
            'message_time' => 'Message Time',
            'subject' => 'Subject',
            'content' => 'Content',
            'replyid' => 'Replyid',
            'type' => 'Type',
        ];
    }
}
