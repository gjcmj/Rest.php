<?php namespace Rest\Http;

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
     * HTTP status codes
     *
     * @var array
     */
    protected static $messages = [

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
     * HTTP status code
     *
     * @var int 
     */
    protected $status;

    /**
     * Http Body
     *
     * @var mixed
     */
    protected $body;

    /**
     * HTTP headers
     *
     * @var array      
     */
    protected $headers = array();

    /**
     * Is to json packet
     * @var boolean
     */
    protected $isJsonPacket = true;

    /**
     * Json encode options
     *
     * @var int 
     */
    protected $jsonEncodeOptions;

    /**
     * Json packet format
     *
     * @var int
     */
    protected $format;

    /**
     * Construct
     *
     * @param array $headers HTTP headers
     * @param int   $status  HTTP status code
     * @param callable $finalizeFunc Output callback
     */
    public function __construct($status = 200, array $headers = [], $format = 0) {
        $this->jsonEncodeOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        $this->status = $status;

        foreach ($headers as $header) {
            $this->setHeader($header);
        }

        $this->format = $format;
    }

    /**
     * Set Body
     *
     * @param mixed $body
     * @param bool  $isJsonPacket
     * @return Rest\Response
     */
    public function write($body, $isJsonPacket = true) {
        $this->body = $body;
        $this->isJsonPacket = $isJsonPacket;

        return $this;
    }

    /**
     * Write
     *
     * set body
     * 
     * @param string|array|object|null $body Content of HTTP response body
     * @return none
     */
    public function send() {
        list($status, $headers, $body) = $this->finalize();

        // status
        header(sprintf('HTTP/%s %s', self::HTTP_VERSION, $this->getMessageForCode($status)));

        // headers
        foreach ($headers as $header) {
            header($header[0], $header[1]);
        }

        // format
        !$this->format || $this->setJsonEncodeOptions($this->getJsonEncodeOptions() | JSON_PRETTY_PRINT);

        // body
        echo $this->isJsonPacket ? $this->json_packet($body) : $body;
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
        $this->status = $status;
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
        // TODO Etag

        // Last Modified
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
        $this->headers[] = [$header, $replace];
        return $this;
    }

    /**
     * Set or unset to json Packet
     *
     * @param bool $flag
     * @return Rest\Response
     */
    public function isJsonPacket($flag = true) {
        $this->isJsonPacket($flag);
        return $this;
    }

    /**
     * Finalize
     *
     * @return [status, headers]
     */
    public function finalize() {
        return [$this->status, $this->headers, $this->body];
    }

    /**
     * Get message for HTTP status code
     *
     * @param  int $status
     * @return string
     * @throw ErrorException
     */
    public static function getMessageForCode($status) {
        if(isset(self::$messages[$status]))
            return $status . ' ' . self::$messages[$status];

        throw new ErrorException('Unknow Http Status '. $status, 500);
    }

    /**
     * Json packet
     *
     * @param string|array $value
     * @return json
     * @throw ErrorException
     */
    public function json_packet($value) {
        if(is_null($value))
            return '';

        $value = json_encode($value, $this->jsonEncodeOptions);

        if(json_last_error() !== JSON_ERROR_NONE)
            throw new ErrorException(json_last_error_msg(), 500);

        return $value;
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
        $this->jsonEncodeOptions = $options;
        return $this;
    }

    /**
     * get json encode options
     *
     * @return int
     */
    public function getJsonEncodeOptions() {
        return $this->jsonEncodeOptions;
    }
}
