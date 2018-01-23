<?php namespace Rest;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * Rest
 * 
 * @package Rest
 * @author ky
 */
class Rest {

    private $_path;

    public function __construct($path) {
        $this->_path = $path;
    }

    public function initialize($timezone = 'PRC') {
        date_default_timezone_set($timezone);
        $this->bootstrapEnvironment();

        Services::bind('request', function() {
            return new \Rest\Http\Request;
        });

        Services::bind('response', function() {
            return new \Rest\Http\Response(200, ['Content-type: application/json;charset=utf-8']);
        });
    }

    public function run() {

        $request = Services::request();
        $request = Services::request();
        var_dump($request);

        /*
         *        set_exception_handler(function($e) {
         *            $this->handleException($e);
         *        });
         *
         *        if (ENVIRONMENT == 'production') {
         *            ini_set("display_errors", 0);
         *
         *            error_reporting(E_ALL ^ E_NOTICE);
         *
         *            set_error_handler($this->handleError, error_reporting());
         *        }
         *
         */
        //require $this->_configPath . '/Routes.php';
    }

    protected function bootstrapEnvironment() {
        define('ENVIRONMENT', getenv('REST_ENV') !== false ? 'testing' 
            : $_SERVER['REST_ENV'] ?? 'production');

        switch(ENVIRONMENT) {
        case 'testing':
        case 'development':
            ini_set('display_errors', 1);
            error_reporting(-1);
            break;

        case 'production':
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
            break;
        }
    }
}
