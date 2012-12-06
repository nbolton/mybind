<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind;

require_once "Controllers/ControllerProvider.php";

class App {

  public function __construct() {
    $this->controllerProvider = new Controllers\ControllerProvider;
  }

  public function run() {
    try {
      $controller = $this->controllerProvider->getForPath();
    }
    catch (Controllers\InvalidPathException $ex) {
      header("HTTP/1.0 404 Not Found");
      echo "<html><body><h1>404: Not Found</h1></body></html>";
      exit;
    }
    
    $controller->run();
  }
}

?>
