<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

abstract class Controller {

  protected function showView($view, $title="", $data=array()) {
    $this->view = $view;
    $title = "MyBind" . ($title == "" ? "" : " - $title");
    $app = $this->app;
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    require_once "php/MyBind/Views/master.php";
  }
  
  protected function hasJavascriptFile() {
    return file_exists("js/$this->view.js");
  }
  
  protected function getJavascriptFilePath() {
    return $this->app->getFilePath("js/$this->view.js");
  }
  
  protected function hasStyleFile() {
    return file_exists("css/$this->view.css");
  }
  
  protected function getStyleFilePath() {
    return $this->app->getFilePath("css/$this->view.css");
  }
  
  protected function isPost() {
    return $_SERVER["REQUEST_METHOD"] == "POST";
  }
}

?>
