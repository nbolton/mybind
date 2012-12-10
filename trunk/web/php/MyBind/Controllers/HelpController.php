<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";

class HelpController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^help\/.*/";
  }
  
  public function run() {
    if (preg_match("/^help\/$/", $this->app->path)) {
      $this->showView("help", "Help");
    }
    else {
      throw new InvalidPathException($this->app->path);
    }
  }
}

?>
