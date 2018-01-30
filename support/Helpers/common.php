<?php
use Rest\Services;

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
        throw new ErrorException($message, substr($message, 0, 3));
    }
}

if (!function_exists('service')) {

    function service(string $name, $share) {
        return Services::$name($share);
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
