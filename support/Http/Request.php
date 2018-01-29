<?php namespace Rest\Http;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * HTTP Request
 * 
 * 仅支持 GET, POST, PUT, DELETE 请求
 *
 * @package Rest\Http
 * @author ky
 */
class Request {

    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * Request params
     *
     * @var array
     */
    protected $params = array();

    /**
     * Construct
     */
    public function __construct() {}

    /**
     * Fetch GET and REQUEST_METHON
     *
     * 返回REUEST_METHOD 的所有参数
     *
     * @param string $key
     * @param string $default Default return value when key does not exist
     * @return array|string|null
     */
    public function params($key = null, $default = null) {
        if (empty($this->params)) {
            switch ($this->getMethod()) {

                case self::METHOD_GET:
                    $this->params = $this->get();
                    break;

                case self::METHOD_POST:
                    $this->params = array_merge($this->get(), $this->post());
                    break;

                case self::METHOD_PUT:
                    $this->params = array_merge($this->get(), $this->put());
                    break;

                case self::METHOD_DELETE:
                    $this->params = array_merge($this->get(), $this->delete());
                    break;

                default:
                    $this->params = array();
            }
        }

        return $this->fetch_from_array($this->params, $key, $default);
    }

    /**
     * GET HTTP Method
     * 
     * @return string
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * HTTP request required params
     *
     * requireParams(['uid' => 'decode_id', 'name']);
     *
     * @param array $arr
     * @return boolean
     */
    public function requiredParams(array $params) {
        $arr = $this->params();
        foreach ($params as $k => $v) {
            if (!isset($arr[is_int($k) ? $v : $k]) || (is_string($k) && !$v($this->params[$k]))) return false; 
        }
        return true;
    }

    /**
     * Fetch the ip address
     *
     * @return string
     */
    public function getIp() {
        $keys = ['HTTP_X_REAL_IP','REMOTE_ADDR', 'X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP'];
        foreach ($keys as $k) {
            if (isset($_SERVER[$k])) return $SERVER[$k];
        }
        return "0.0.0.0";
    }

    /**
     * Fetch client type
     *
     * @return string
     */
    public function getClientType() {
        $arr = explode(';', $this->getUserAgent(), 5);
        return count($arr) == 5 && in_array($arr[2], ['Android', 'iPhone OS']) ? $arr[2] : null;
    }

    /**
     * Fetch client version
     *
     * @return string
     */
    public function getClientVersion() {
        $arr = explode(';', $this->getUserAgent(), 5);
        return count($arr) == 5 ? $arr[1] : null;
    }

    /**
     * Fetch user agent
     *
     * @return string
     */
    public function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?: "";
    }

    /**
     * Is ajax Request ?
     *
     * @return boolean
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
    }

    /**
     * Fetch an item from array
     *
     * @param array  $array
     * @param string $key
     * @param string $default
     * @return mixed
     */
    protected function fetch_from_array ($array, $key = null, $default = null) {
        return is_null($key) ? $array : 
            (!empty($array[$key]) ? $array[$key] : $default);
    }

    /**
     * Fetch GET data
     *
     * @param string $key
     * @param string $default Default return value when key does not exist
     * @return array|string|null
     */
    protected function get($key = null, $default = null) {
        mb_parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $arr);

        return $this->fetch_from_array($arr, $key, $default);
    }

    /**
     * Fetch POST data
     *
     * @param string $key
     * @param string $default Default return value when key does not exist
     * @return array|string|null
     */
    protected function post($key = null, $default = null) {
        parse_str(file_get_contents('php://input'), $arr);

        return $this->fetch_from_array($arr, $key, $default);
    }

    /**
     * Fetch PUT data
     *
     * @param string $key
     * @param string $default Default return value when key does not exist
     * @return array|string|null
     */
    protected function put($key = null, $default = null) {
        parse_str(file_get_contents('php://input'), $arr);

        return $this->fetch_from_array($arr, $key, $default);
    }

    /**
     * Fetch DELETE data
     *
     * @param string $key
     * @param string $default Default return value when key does not exist
     * @return array|string|null
     */
    protected function delete($key = null, $default = null) {
        parse_str(file_get_contents('php://input'), $arr);

        return $this->fetch_from_array($arr, $key, $default);
    }
}
