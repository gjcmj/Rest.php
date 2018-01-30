<?php namespace Rest;

use Rest\Http\Response;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * Exceptions
 * 
 * @package Rest
 * @author ky
 */
class Exceptions {

    /**
     * @var \Rest\Http\Response
     */
    protected $response;

    /**
     * Custom response format
     *
     * @var Callable
     */
    protected $outputCallback;

    /**
     * Construct
     
     * @param \Rest\Http\Response
     */
    public function __construct(Response $response) {
        $this->response = $response;
    }

    /**
     * init
     *
     * @return void
     */
    public function initialize(Callable $callback) {
        $this->outputCallback = $callback;

        set_exception_handler([$this, 'exceptionHandler']);

        set_error_handler([$this, 'errorHandler']);

        register_shutdown_function([$this, 'shutdownHandler']);
    }

    /**
     * Exception Handler
     *
     * @param \Throwable
     * @return void
     */
    public function exceptionHandler(\Throwable $e) {
        $code = $e->getCode();
        $statusCode = $this->determineCode($code) ;
        $message = $e->getMessage();

        if($code == 0) {
            error_log($message . ' in '
                . $e->getFile()
                . ':' .$e->getLine()
                . "\nStack trace:\n". $e->getTraceAsString() . "\n thrown in"
                . $e->getFile() . ' on line ' . $e->getLine(), error_reporting());
        }

        $this->response
            ->setStatus($statusCode)
            ->write($this->outputCallback ? ($this->outputCallback)($message) : $message);

        exit(1);
    }

    /**
     * Error Handler
     *
     * @param int $num
     * @param String $str
     * @param String $file
     * @param int $line
     * @param String $context
     *
     * @return void
     * @throw \ErrorException
     */
    public function errorHandler($num, $str, $file, $line, $context = null) {
        throw new \ErrorException($str, 0, $num, $file, $line);
    }

    /**
     * Shutdown Handler
     *
     * @return void
     */
    public function shutdownHandler() {
        $error = error_get_last();

        // Fatal Error
        if(!is_null($error) && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_PARSE])) {
            $this->exceptionHandler(new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }
    }

    /**
     * Determine Code
     *
     * @return int
     */
    protected function determineCode($statusCode) {
        return ($statusCode < 100 || $statusCode > 599) ? 500 : $statusCode;
    } 
}
