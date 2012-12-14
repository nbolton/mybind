<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

class SecurityException extends \Exception {
  public function __construct($message) {
    parent::__construct($message);
  }
}

?>
