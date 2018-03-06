<?php namespace Rest;

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
    private $config;

    /**
     * database object
     *
     * @var array
     */
    private $db = array();

    /**
     * database factory
     *
     * @param string $name Database name
     * @param string $type Database type
     * @return object | null
     * @throw ErrorException
     */
    public function connection($name, $type) {
        if(!$this->config) {
            $this->config = require CONFIG_PATH . '/Database/' . ENVIRONMENT  . '.php';
        }

        if (!empty($this->db[$type][$name])) {
            return $this->db[$type][$name];
        }

        if (isset($this->config[$type][$name])) {
            $config = $this->config[$type][$name];

            switch ($type) {

                case 'redis':
                    $connect = $config['pconnect'] ? 'pconnect' : 'connect';

                    $this->db[$type][$name] = new Redis();
                    $this->db[$type][$name]->$connect($config['host'], $config['port'], $config['timeout']);
                    break;

                case 'mongodb':
                    $this->db[$type][$name] = (new MongoClient($config['dsn'], $config['options']))->$config['dbname'];
                    break;

                case 'mysql':
                    $this->db[$type][$name] = new PDO($config['dsn'], $config['username'], $config['password'], $config['options']);
                    break;
            }

            return $this->db[$type][$name];
        }

        throw new \ErrorException('Unknow database source', 500);
    }
}
