<?php namespace Rest;

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
    }
}
