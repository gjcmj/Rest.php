<?php namespace App\Controllers;

use Rest\Controller;

class Demo extends Controller {

    public function index($id) {
        echo $id;
    }
}
