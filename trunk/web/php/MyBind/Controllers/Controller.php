<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

abstract class Controller {

  function showView($page, $title="", $data=array()) {
    $title = "MyBind" . ($title == "" ? "" : " - $title");
    $app = $this->app;
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    require_once "php/MyBind/Views/master.php";
  }
}

?>
