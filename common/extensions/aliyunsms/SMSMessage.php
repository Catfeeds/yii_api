<?php
namespace common\extensions\aliyunsms;

require_once ('mns-autoloader.php');
use AliyunMNS\Client;
use AliyunMNS\Topic;
use AliyunMNS\Constants;
use AliyunMNS\Model\MailAttributes;
use AliyunMNS\Model\SmsAttributes;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\PublishMessageRequest;
use yii\base\Component;

class SMSMessage extends Component
{

    public $endPoint = '';
    public $accessId = '';
    public $accessKey = '';
    public $topicName = '';
    public $client = '';
    public $signname = '';

    public function init()
    {}

    public function send($mobile, $SMSTemplateCode, $code, $signname)
    {
        /**
         * Step 1.
         * 初始化Client
         */
        // $this->endPoint = "http://31231402.mns.cn-shanghai.aliyuncs.com"; // eg. http://1234567890123456.mns.cn-shenzhen.aliyuncs.com
        // $this->accessId = "S8Q4N7VR9jICCp3B";
        // $this->accessKey = "gZGRoi0IEwE1lmGX9TTfTXSPWqyK3m";
        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
        /**
         * Step 2.
         * 获取主题引用
         */
        $topicName = "sms.topic-cn-shanghai";
        $topic = $this->client->getTopicRef($topicName);
        /**
         * Step 3.
         * 生成SMS消息属性
         */
        // 3.1 设置发送短信的签名（SMSSignName）和模板（SMSTemplateCode）
        $batchSmsAttributes = new BatchSmsAttributes($signname, $SMSTemplateCode);
        // 3.2 （如果在短信模板中定义了参数）指定短信模板中对应参数的值
        $batchSmsAttributes->addReceiver($mobile, array(
            "code" => $code
        ));
        $messageAttributes = new MessageAttributes(array(
            $batchSmsAttributes
        ));
        /**
         * Step 4.
         * 设置SMS消息体（必须）
         *
         * 注：目前暂时不支持消息内容为空，需要指定消息内容，不为空即可。
         */
        $messageBody = "smsmessage";
        /**
         * Step 5.
         * 发布SMS消息
         */
        $request = new PublishMessageRequest($messageBody, $messageAttributes);
        try {
            $res = $topic->publishMessage($request);
            echo $res->isSucceed();
            echo "\n";
            echo $res->getMessageId();
            echo "\n";
        } catch (MnsException $e) {
            echo $e;
            echo "\n";
        }
    }
}
// $instance = new SMSMessage();
// $instance->run();
?>