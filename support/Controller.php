<?php namespace Rest;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * Controller
 *
 * @package Rest
 * @author ky
 */
class Controller {

    /**
     * GET Request/response Instance
     *
     * @params string $key
     * @return mixed
     */
    public function __get($key) {
        if($key == 'request')
            return $this->request = Services::request();

        if($key == 'response') 
            return $this->response = Services::response();
    }
}
