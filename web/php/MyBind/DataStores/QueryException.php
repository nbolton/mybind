<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

class QueryException extends \Exception {
  public function __construct($error) {
    parent::__construct($error);
  }
}

?>
