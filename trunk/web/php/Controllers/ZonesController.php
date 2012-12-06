<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";

class ZonesController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^zones\/.*/";
  }
  
  public function run() {
    $this->showView("zones/index", "Zones");
  }
}

?>
