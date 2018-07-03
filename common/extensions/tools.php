<?php
/**
* @date        : 2017年11月20日
* @author      : sun
* @copyright   : http://www.hoge.cn/
* @description :
*/
namespace common\utils;

class tools
{

/**
 * @param $lat1
 * @param $lon1
 * @param $lat2
 * @param $lon2
 * @param float $radius  星球半径
 * @return float
 * 经纬度计算距离
 *
$lat1 = '31.253411';
$lon1 = '121.518998';
$lat2 = '31.277117';
$lon2 = '120.744587';
echo distance($lat1, $lon1, $lat2, $lon2);   // 73.734589823354
 */
function distance($lat1, $lon1, $lat2,$lon2,$radius = 6378.137)
{
    $rad = floatval(M_PI / 180.0);

    $lat1 = floatval($lat1) * $rad;
    $lon1 = floatval($lon1) * $rad;
    $lat2 = floatval($lat2) * $rad;
    $lon2 = floatval($lon2) * $rad;

    $theta = $lon2 - $lon1;

    $dist = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));

    if ($dist < 0 ) {
        $dist += M_PI;
    }
    return $dist = $dist * $radius;
}


}

