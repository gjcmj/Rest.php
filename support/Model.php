<?php namespace Rest;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * Model
 *
 * @package Rest
 * @author ky
 */

class Model {

    /**
     * database config
     *
     * @var array
     */
    private $_config = array();

    /**
     * database object
     *
     * @var array
     */
    private $_DB = array();

    /**
     * Construct
     */
    public function __construct() {
        switch ($_SERVER['APPLICATION_ENV']) {
            case 'production':
                $config = '../config/database.php';
                break;

            case 'testing':
                $config = '../config/database-testing.php';
                break;

            default:
                $config = '../config/database-dev.php';
        }

        $this->_config = require $config;
    }

    /**
     * database factory
     *
     * @param string $db_name Database name
     * @param string $db_type Database type
     * @return object | null
     */
    public function connection($db_name, $db_type) {
        if (!empty($this->_DB[$db_type][$db_name])) return $this->_DB[$db_type][$db_name];

        if (isset($this->_config[$db_type][$db_name])) {
            $config = $this->_config[$db_type][$db_name];

            switch ($db_type) {

                case 'redis':
                    $connect = $config['pconnect'] ? 'pconnect' : 'connect';

                    $this->_DB[$db_type][$db_name] = new Redis();
                    $this->_DB[$db_type][$db_name]->$connect($config['host'], $config['port'], $config['timeout']);
                    break;

                case 'mongodb':
                    $this->_DB[$db_type][$db_name] = (new MongoClient($config['dsn'], $config['options']))->$config['dbname'];
                    break;

                case 'mysql':
                    $this->_DB[$db_type][$db_name] = new PDO($config['dsn'], $config['username'], $config['password'], $config['options']);
                    break;
            }

            return $this->_DB[$db_type][$db_name];
        }

        throw_exception(Errors::UNKNOWN_DATABASE_SOURCE);
    }

    /**
     * Destruct
     */
//    public function __destruct() {
//
//        // release DB
//        //$this->_releaseDB();
//    }
//
//    /**
//     * release DB connect
//     *
//     * MongoDB, Mysql 均不用明确close
//     *
//     * @return void
//     */
//    protected function _releaseDB() {
//
//        // redis
//        if ($this->_DB['redis']) {
//            foreach ($this->_DB['redis'] as $k => $v) {
//                if (!$this->_config['redis'][$k]['pconnect']) $this->_DB['redis'][$k]->close();
//            }
//        }
//    }
}
