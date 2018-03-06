<?php namespace App\Demo;

use App\Config\Errors;
use Rest\Controller;

class DemoController extends Controller {

    private $model;

    /**
     * 自动注入 App\Demo\DemoModel 实例
     */
    public function __construct(DemoModel $model) {
        $this->model = $model;
    }

    public function index($id, $name) {
        $name == 'test' || throw_exception(Errors::BAD_REQUEST);

        return $this->model->test($id, $name);
    }
}
