<?php namespace Rest\Http;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * HTTP Response
 * 
 * @package Rest\Http
 * @author ky
 */
class Response {

    /**
     * @const string
     */
    const HTTP_VERSION = 1.1;

    /**
     * @var array HTTP response codes and messages
     */
    private static $_messages = [

        //Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        //Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        //Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',

        //Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',

        //Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ];

    /**
     * @var int HTTP status code
     */
    private $_status;

    /**
     * @var array HTTP headers
     */
    private $_headers = array();

    /**
     * @var Content of HTTP response body
     */
    private $_body;

    /**
     * @var  json_encode options
     */
    private $_jsonEncodeOptions = null;

    /**
     * @var body json packet
     */
    private $_bodyJsonPacket = true;

    /**
     * Construct
     *
     * @param array $_headers HTTP headers
     * @param int   $_status  HTTP status code
     * @param callable $finalizeFunc Output callback
     */
    public function __construct($status = 200, array $headers = [], $body = '') {
        
        $this->_jsonEncodeOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        $this->_status = $status;

        foreach ($headers as $header) {
            $this->setHeader($header);
        }

        $this->_body = $body;
    }

    /**
     * Write
     *
     * set body
     * 
     * @param string|array|object|null $_body Content of HTTP response body
     * @return none
     */
    public function write($body, $bodyJsonPacket = true) {
        $this->_body = $body;
        $this->_bodyJsonPacket = $bodyJsonPacket;
    }

    /**
     * Set status 
     *
     * http status
     *
     * @params int $status
     * @return this
     */
    public function setStatus($status) {
        $this->_status = $status;
        return $this;
    }

    /**
     * Cache time
     * 
     * proxy cache
     *
     * @param int $time. Default 10 minute
     * @return void
     */
    public function cache($times = 600) {
        $this->setHeader('Last-Modified: ' . gmdate("D, d M Y H:i:s", time()) . ' GMT')
            ->setHeader('Cache-Control: public, max-age=' . (is_numeric($times) ? $times : 0));
    }

    /**
     * Set HTTP header
     *
     * @param string $header 完整header 
     * @param boolean $replace 是否允许相同的头
     * @return this
     */
    public function setHeader($header, $replace = true) {
        $this->_headers[] = [$header, $replace];
        return $this;
    }

    /**
     * Finalize
     *
     * @return [status, headers, body]
     */
    public function finalize() {
        return [$this->_status, $this->_headers, $this->_body];
    }

    /**
     * Get message for HTTP status code
     *
     * @param  int $_status
     * @return string|null
     */
    public static function getMessageForCode($status) {
        return isset(self::$_messages[$status]) ? self::$_messages[$status] : null;
    }

    /**
     * Json packet
     *
     * @param string|array $value
     * @return json
     */
    public function json_packet($value, $code = 0) {

        $value = ($code == 0) ? json_encode($value, $this->_jsonEncodeOptions) : json_encode(['code' => $code, 'message' => $value], $this->_jsonEncodeOptions);

        return json_last_error() == JSON_ERROR_NONE ? $value : 
            sprintf('{"code" : %s, "message" : %s}', substr(Error::JSON_ENCODE_ERROR, 0, 8), substr(Error::JSON_ENCODE_ERROR, 9));
    }

    /**
     * Set Json encode options
     *
     * see http://php.net/manual/en/json.constants.php
     *
     * @params int $options
     * @return this
     */ 
    public function setJsonEncodeOptions($options) {
        $this->_jsonEncodeOptions = $options;
        return $this;
    }

    /**
     * get json encode options
     *
     * @return int
     */
    public function getJsonEncodeOptions() {
        return $this->_jsonEncodeOptions;
    }

    /**
     * get body json packet
     *
     * @return boolean
     */
    public function getBodyJsonPacket() {
        return $this->_bodyJsonPacket;
    }

    /**
     * get body
     *
     * @return String
     */
    public function getBody() {
        return $this->_body;
    }
}
