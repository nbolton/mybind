<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "IndexController.php";
require_once "ZonesController.php";
require_once "InvalidPathException.php";

class ControllerProvider {

  public function __construct() {
    $this->controllers = array();
    $this->add(new IndexController);
    $this->add(new ZonesController);
  }

  public function getForPath() {
    $path = isset($_GET["path"]) ? $_GET["path"] : "";
    foreach ($this->controllers as $controller) {
      if (preg_match($controller->pathRegex, $path)) {
        return $controller;
      }
    }
    throw new InvalidPathException($path);
  }
  
  private function add($controller) {
    array_push($this->controllers, $controller);
  }
}

?>
