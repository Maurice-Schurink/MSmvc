<?php

namespace App\controllers;

use App\models\testModel;
use App\system\databases\MS_migrationBuilder;
use App\system\MS_controller;

class example extends MS_controller {

    public function index() {
        $test = new MS_migrationBuilder(new testModel());
        var_dump($test);
        echo 123;
        //  echo $test->getShortModelName();
    }
}