<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Models;

class DnsRecord {
  public $id;
  public $zoneId;
  public $name;
  public $type;
  public $ttl;
  public $aux;
  public $data;
}

?>
