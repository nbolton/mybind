<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

class InvalidPathException extends \Exception {
  public function __construct($path) {
    parent::__construct("Controller not found for path: '$path'");
  }
}

?>
