<?php namespace App\Config;

class Errors {

    /****** 1 系统级错误, 00/01 (lua/php), 00 具体错误, xxx HTTP status ******/
    // error_reporting 引发的错误, E_ERROR、 E_PARSE、 E_CORE_ERROR、 E_CORE_WARNING、 E_COMPILE_ERROR、 E_COMPILE_WARNING 除外
    const INTERNAL_ERROR = '50010100 system error';

    const JSON_ENCODE_ERROR = '50010101 json encode error';

    const BAD_REQUEST = '40410102 bad request';

    // 缺少必要参数
    const MISS_PARAMETER = '40010103 miss required parameter';

    // 外部服务错误
    const EXTERNAL_SERVICE_ERROR = '50010104 external service error';

    // redis, mongodb, mysql connect
    const NOSQL_WENT_AWAY = '50010107 nosql server went away';

    const UNKNOWN_DATABASE_SOURCE = '50010105 unknow database source';

    //mysql错误
    const MYSQL_EXECUTE_ERROR = '50010106 mysql execute error';

    /******* 2 服务级错误, 00(公共模块) 00 具体错误, xxx HTTP status ******/
    const INVALID_PARAMETER = '40020000 parameter (%s) value invalid';

    const DUPLICATE_CONTENT = '40020001 duplicate content';
}
