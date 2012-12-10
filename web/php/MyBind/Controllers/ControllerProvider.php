<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "IndexController.php";
require_once "ZonesController.php";
require_once "AccountController.php";
require_once "AboutController.php";
require_once "HelpController.php";
require_once "InvalidPathException.php";

class ControllerProvider {

  public function __construct($app) {
    $this->app = $app;
    $this->controllers = array();
    
    $this->add(new IndexController);
    $this->add(new ZonesController);
    $this->add(new AccountController);
    $this->add(new AboutController);
    $this->add(new HelpController);
  }

  public function getForPath() {
    foreach ($this->controllers as $controller) {
      if (preg_match($controller->pathRegex, $this->app->path)) {
        $controller->app = $this->app;
        return $controller;
      }
    }
    throw new InvalidPathException($this->app->path);
  }
  
  private function add($controller) {
    array_push($this->controllers, $controller);
  }
}

?>
