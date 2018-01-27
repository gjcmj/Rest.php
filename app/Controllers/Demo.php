<?php namespace App\Controllers;

use Rest\Controller;

class Demo extends Controller {

    public function index($id = '123456') {
        echo $id;
    }
}
