<?php
namespace common\extensions;

use yii;

/**
 * @author : Jason
 * @description : 快递鸟接口
 *
 */
class kdniao
{

    private $ebusinessid;
    // 商户ID
    private $appkey;
    // 商户秘钥
    private $request_type;
    // 请求类型
    private $request_url;
    // 请求URL
    /**
     * 构造函数
     */
    public function __construct()
    {
//         $config=new Config();
//         $express_config=$config->getOrderExpressMessageConfig($shop_id);
        
        $this->ebusinessid = Yii::$app->params['kdniao']['ebusinessid'];
        $this->appkey = Yii::$app->params['kdniao']['appkey'];
        $this->request_type = 1002;
        //$this->request_url = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';
        $this->request_url = 'http://api.kdniao.cc/api/dist';
    }

    /**
     * Json方式 查询订单物流轨迹
     * $ShipperCode 快递编号
     * $LogisticCode 快递单号
     *
     */
    public function getOrderTracesByJson($ShipperCode, $LogisticCode)
    {
        $requestData = json_encode([
            'OrderCode' => '',
            'ShipperCode' => $ShipperCode,
            'LogisticCode' => $LogisticCode,
        ]);
        $datas = array(
            'EBusinessID' => $this->ebusinessid,
            'RequestType' => $this->request_type,
            'RequestData' => urlencode($requestData),
            'DataType' => '2'
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->appkey);
        $result = $this->sendPost($this->request_url, $datas);
        // 根据公司业务处理返回的信息......
        return $result;
    }

    /**
     * post提交数据
     *
     * @param string $url
     *            请求Url
     * @param array $datas
     *            提交的数据
     * @return url响应返回的html
     */
    public function sendPost($url, $datas)
    {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if (empty($url_info['port'])) {
            $url_info['port'] = 80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        $httpheader .= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (! feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (! feof($fd)) {
            $gets .= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     *
     * @param
     *            data 内容
     * @param
     *            appkey Appkey
     * @return DataSign签名
     */
    public function encrypt($data, $appkey)
    {
        return urlencode(base64_encode(md5($data . $appkey)));
    }

}