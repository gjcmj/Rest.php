<?php
/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * 公共助手函数库
 * 每次请求都会载入
 */

/**
 * throw exception
 *
 * @param string $message
 * @throws ErrorException
 */
if(!function_exists('throw_exception')) {

    function throw_exception($message) {
        list($code, $msg) = [substr($message, 0, 8), substr($message, 9)];
        throw new ErrorException($msg, $code);
    }
}

/**
 * xss 攻击过滤(仅做了strip_tags)
 * 
 * @params string $data 待过滤字符串
 * @return string
 */
if(!function_exists('xss_clean')){

    function xss_clean(&$data) {
        return strip_tags(trim($data));
    }
}

/**
 * 获得系统毫秒时间戳
 *
 * @return float
 */
if(!function_exists('getMillisecond')) {

    function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}
