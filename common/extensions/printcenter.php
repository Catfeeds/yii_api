<?php 
namespace common\extensions;

class printcenter{
    private $printurl = 'http://open.printcenter.cn:8080/addOrder';
    private $statusurl = 'http://open.printcenter.cn:8080/queryPrinterStatus';
    /**
     * 发送打印信息
     */
    public function sendPrint($deviceNo,$key,$content,$times='1')
    {
        $header=["Content-Type: text/html"=>"charset=utf-8"];
        
        $post_data = array (
            'deviceNo' => $deviceNo,
            'key' => $key,
            'printContent' => $content,
            'times' => $times,
        );
        $post_data = http_build_query($post_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $this->printurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        return $output;
    }
    /**
     * 打印机状态
     */
    public function getStatus($deviceNo,$key)
    {
        $header=["Content-Type: text/html"=>"charset=utf-8"];
        $post_data = array (
            'deviceNo' => $deviceNo,
            'key' => $key
        );
        $post_data = http_build_query($post_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $this->statusurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        return $output;
    }
}
 ?>