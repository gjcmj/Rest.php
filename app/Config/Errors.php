<?php namespace App\Config;

class Errors {

    /**
     * - 500 (HTTP Status Code)
     * - 1   (1 系统级错误 / 2 服务级错误)
     * - 01  (00 lua / xx 模块)
     * - 00  (具体错误)
     */
    const INTERNAL_ERROR = '500 10100 Internal system error';

    const BAD_REQUEST    = '404 20100 Bad request';

    const MISS_PARAMETER = '400 20101 Miss required parameter';
}
