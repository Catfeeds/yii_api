<?php
/**
 * 系统环境与变量处理工具类文件
 *
 * @author banyanCheung <banyan@ibos.com.cn>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2012-2013 IBOS Inc
 */
/**
 * 系统环境与变量处理工具类库
 *
 * @package application.core.utils
 * @author banyanCheung <banyan@ibos.com.cn>
 * @version $Id: env.php -1 $
 */
namespace common\utils;

class Env
{

    /**
     * 手机浏览器列表
     *
     * @staticvar array
     */
    private static $mobileBrowserList = array(
        'iphone',
        'android',
        'phone',
        'mobile',
        'wap',
        'netfront',
        'java',
        'opera mobi',
        'opera mini',
        'ucweb',
        'windows ce',
        'symbian',
        'series',
        'webos',
        'sony',
        'blackberry',
        'dopod',
        'nokia',
        'samsung',
        'palmsource',
        'xda',
        'pieplus',
        'meizu',
        'midp',
        'cldc',
        'motorola',
        'foma',
        'docomo',
        'up.browser',
        'up.link',
        'blazer',
        'helio',
        'hosin',
        'huawei',
        'novarra',
        'coolpad',
        'webos',
        'techfaith',
        'palmsource',
        'alcatel',
        'amoi',
        'ktouch',
        'nexian',
        'ericsson',
        'philips',
        'sagem',
        'wellcom',
        'bunjalloo',
        'maui',
        'smartphone',
        'iemobile',
        'spice',
        'bird',
        'zte-',
        'longcos',
        'pantech',
        'gionee',
        'portalmmm',
        'jig browser',
        'hiptop',
        'benq',
        'haier',
        '^lct',
        '320x320',
        '240x320',
        '176x220'
    );

    /**
     * 平板标识列表
     *
     * @staticvar array
     */
    private static $padList = array(
        'pad',
        'gt-p1000'
    );

    /**
     * 检查是否以手机进入
     *
     * @staticvar array $mobileBrowserList 手机所用的浏览器列表
     * @return boolean 是否以手机进入
     */
    public static function checkInMobile()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        // 先检查是否Pad
        if (StringUtil::istrpos($userAgent, self::$padList)) {
            return false;
        }
        $value = StringUtil::istrpos($userAgent, self::$mobileBrowserList, true);
        if ($value) {
            Ibos::app()->setting->set('mobile', $value);
            return true;
        }
        return false;
    }

    /**
     * 检查是否以手机APP进入
     *
     * @return boolean 是否以手机APP进入
     */
    public static function checkInApp()
    {
        return defined('MODULE_NAME') && MODULE_NAME == 'mobile' ? true : false;
    }

    /**
     * 获取用户加密的8位身份标识字符串
     *
     * @param string $specialadd            
     * @return string
     */
    public static function formHash()
    {
        $global = Ibos::app()->setting->toArray();
        $hashAdd = defined('IN_DASHBOARD') ? 'Only For IBOS Admin DASHBOARD' : '';
        return substr(md5(substr($global['timestamp'], 0, - 7) . Ibos::app()->user->uid . $global['authkey'] . $hashAdd), 8, 8);
    }

    /**
     * 格式化header输出退出信息
     *
     * @param string $msg            
     */
    public static function iExit($msg = 0)
    {
        header('Content-Type:text/html; charset=' . CHARSET);
        exit($msg);
    }

    /**
     * 是否https链接
     *
     * @return boolean
     */
    public static function isHttps()
    {
        return (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') ? true : false;
    }

    /**
     * 获取网站域名
     *
     * @return boolean
     */
    function get_domain()
    {
        /* 协议 */
        $protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
        
        /* 域名或IP地址 */
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            /* 端口 */
            if (isset($_SERVER['SERVER_PORT'])) {
                $port = ':' . $_SERVER['SERVER_PORT'];
                
                if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
                    $port = '';
                }
            } else {
                $port = '';
            }
            
            if (isset($_SERVER['SERVER_NAME'])) {
                $host = $_SERVER['SERVER_NAME'] . $port;
            } elseif (isset($_SERVER['SERVER_ADDR'])) {
                $host = $_SERVER['SERVER_ADDR'] . $port;
            }
        }
        return $protocol . $host;
    }

    /**
     * 获取站点url
     *
     * @param boolean $isHttps            
     * @param string $sitePath            
     * @return string
     */
    public static function getSiteUrl($isHttps = false, $sitePath = '')
    {
        if (empty($sitePath)) {
            $phpself = self::getScriptUrl();
            $sitePath = substr($phpself, 0, strrpos($phpself, '/'));
        }
        return StringUtil::ihtmlSpecialChars('http' . ($isHttps ? 's' : '') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $sitePath . '/');
    }

    /**
     * 判断当前是否是微信浏览器
     */
    public static function isWeixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return 1;
        }
        return 0;
    }

    /**
     * 获取当前用户的IP
     *
     * @return string
     */
    public static function getClientIp()
    {
        if (! isset($_SERVER['REMOTE_ADDR'])) {
            return 'unknow';
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        if (getenv('HTTP_CLIENT_IP')) {
            $clientIp = getenv('HTTP_CLIENT_IP');
            $matcheClientIp = preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $clientIp);
            if ($matcheClientIp) {
                $ip = $clientIp;
            }
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (! preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        $ip = $ip == '::1' ? '127.0.0.1' : $ip;
        return $ip;
    }
}
