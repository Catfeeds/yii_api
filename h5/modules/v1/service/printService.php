<?php
namespace h5\modules\v1\service;

use Yii;
use backend\modules\restaurant\models\Prints;
use backend\modules\restaurant\models\PrintTemplate;
use common\extensions\ylyprint;
use backend\modules\admin\models\Site;
use common\extensions\printcenter;
use backend\modules\restaurant\models\FoodOrder;
use backend\modules\restaurant\models\OrderFood;
use backend\modules\restaurant\models\OrderAddress;
use backend\modules\mall\models\AreaRegion;
use backend\modules\mall\models\Region;

/**
 * 打印服务
 */
class printService
{
    public static function getTemplate($id,$site_id,$model)
    {
        $prints = Prints::findOne(['id' => $id,'site_id' => $site_id]);
        if (empty($prints)) 
        {
            return false;
        }
        $template = PrintTemplate::findOne(['brand' => $prints['brand'],'model' => $model]);
        return $template;
    }
    
    public static function OutAllWorkPrint($order_sn)
    {        
        $order = FoodOrder::find()->select(['order_id','site_id','order_price','box_price','people','shipping_price','note','deliverytime'])->where(['order_sn'=>$order_sn])->asArray()->one();
        $orderaddress = OrderAddress::findOne(['order_sn'=>$order_sn]);
        $orderfood = OrderFood::find()->select(['name','num','price','sku_name'])->where(['order_id'=>$order['order_id']])->asArray()->all();
        $site = Site::findOne(['site_id'=>$order['site_id']]);
        $address = Region::getRegions($orderaddress->twon);
        
        $address .= $orderaddress->address.' ';
        $address .= $orderaddress->consignee;
        
        
        $theconfigs = Prints::findAll(['site_id' => $order['site_id'],'status' => Prints::WORK_PRINT]);
        
        $model = Prints::OUT_PRINT;
        $cotent ='';
        foreach ($theconfigs as $theconfig) {
            if ($theconfig->brand == 1) {
                $template = PrintTemplate::findOne(['brand' => $theconfig->brand,'model' => $model]);
                if(empty($template)) continue;
                foreach ( $orderfood as $msg){
                    $cotent .= $msg['name']. '\r' . $msg['num']. ' ' . $msg['price']. ' ' . $msg['num']. '\r';
                }
                
                $template = str_replace("TITLE", $site['name'], $template->template);
                $template = str_replace("ID", $order_sn, $template);
                $template = str_replace("FOOD",$cotent, $template);
                $template = str_replace("ORDERPRICE", $order['order_price']+ $order['box_price'], $template);
                $template = str_replace("PHONE", $orderaddress->mobile, $template);
                $template = str_replace("ADDRESS", $address, $template);
                $template = str_replace("NOTE", $order['note'], $template);
                $template = str_replace("TIME", $order['deliverytime'], $template);
                
                $ypring = new ylyprint();
                $config = Yii::$app->params['yiliany'];
                $ypring->action_print($config['user_id'], $theconfig->eq_number, $template, $config['api'], $theconfig->eq_key);
                
            } elseif ($theconfig->brand == 2) {
                $template = PrintTemplate::findOne(['brand' => $theconfig->brand,'model' => $model]);
                if(empty($template)) continue;
                foreach ( $orderfood as $msg){
                    $cotent .= $msg['name']."　　　　　 ".$msg['price']/$msg['num']." ".$msg['num']." ".$msg['price']."<BR>";
                }
                $template = str_replace("TITLE", $site['name'], $template->template);
                $template = str_replace("ID", $order_sn, $template);
                $template = str_replace("FOOD",$cotent, $template);
                $template = str_replace("SHIPPRICE", $order['shipping_price'], $template);
                $template = str_replace("BOXPRICE", $order['box_price'], $template);
                $template = str_replace("ORDERPRICE", $order['order_price']+ $order['box_price']+$order['shipping_price'], $template);
                $template = str_replace("PHONE", $orderaddress->mobile, $template);
                $template = str_replace("ADDRESS", $address, $template);
                $template = str_replace("NOTE", $order['note'], $template);
                $template = str_replace("TIME", date('m月d号 H:i', $order['deliverytime']), $template);
                $printcenter = new printcenter();
                $printcenter->sendPrint($theconfig->eq_number, $theconfig->eq_key, $template);
            }
        }
        return true;
    }
}