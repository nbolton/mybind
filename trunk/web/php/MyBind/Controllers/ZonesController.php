<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";
require_once "php/MyBind/DataStores/DnsZoneDataStore.php";

class ZonesController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^zones\/.*/";
  }
  
  public function run() {
    $ds = new \MyBind\DataStores\DnsZoneDataStore;
    $data["zones"] = $ds->getAll();
    $this->showView("zones/index", "Zones", $data);
  }
}

?>
