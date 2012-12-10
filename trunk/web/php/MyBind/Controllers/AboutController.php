<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";

class AboutController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^about\/.*/";
  }
  
  public function run() {
    if (preg_match("/^about\/$/", $this->app->path)) {
      $this->showView("about", "About");
    }
    else {
      throw new InvalidPathException($this->app->path);
    }
  }
}

?>
