<?php namespace App\Demo;

use Rest\Model;

class DemoModel extends Model {

    public function test($id, $name) {
        return [$id => $name];
    }
}
