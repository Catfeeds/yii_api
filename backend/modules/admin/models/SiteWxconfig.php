<?php

namespace backend\modules\admin\models;

use Yii;

/**
 * This is the model class for table "{{%site_wxconfig}}".
 *
 * @property integer $id
 * @property integer $site_id
 * @property integer $type
 * @property string $appid
 * @property string $appsecret
 * @property string $mch_id
 * @property string $partnerkey
 * @property string $ssl_cer
 * @property string $ssl_key
 * @property string $cachepath
 */
class SiteWxconfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_wxconfig}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'appid', 'appsecret', 'mch_id', 'partnerkey', 'ssl_cer', 'ssl_key', 'cachepath'], 'required'],
            [['site_id', 'type'], 'integer'],
            [['appid'], 'string', 'max' => 32],
            [['appsecret', 'mch_id', 'partnerkey'], 'string', 'max' => 64],
            [['ssl_cer', 'ssl_key', 'cachepath'], 'string', 'max' => 254],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'type' => 'Type',
            'appid' => 'Appid',
            'appsecret' => 'Appsecret',
            'mch_id' => 'Mch ID',
            'partnerkey' => 'Partnerkey',
            'ssl_cer' => 'Ssl Cer',
            'ssl_key' => 'Ssl Key',
            'cachepath' => 'Cachepath',
        ];
    }
}
