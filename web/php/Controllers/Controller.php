<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

class Controller {

  function showView($page, $title="MyBind", $data=array()) {
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    require_once "php/Views/master.php";
  }
}

?>
