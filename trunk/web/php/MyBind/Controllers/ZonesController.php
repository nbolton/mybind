<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";
require_once "php/MyBind/DataStores/DnsZoneDataStore.php";
require_once "php/MyBind/DataStores/DnsRecordDataStore.php";

class ZonesController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^zones\/.*/";
  }
  
  public function run() {
    if (preg_match("/^zones\/edit\/(\d+)\/$/", $this->app->path, $m)) {
      $this->runEdit((int)$m[1]);
    }
    else if (preg_match("/^zones\/$/", $this->app->path)) {
      $this->runIndex();
    }
    else {
      $this->app->showPageNotFound();
    }
  }
  
  private function runIndex() {
    $ds = new \MyBind\DataStores\DnsZoneDataStore;
    
    if (isset($_GET["showAll"])) {
      $data["zones"] = $ds->getAll();
    }
    else {
      $data["zones"] = $ds->getByUserId(1);
    }
    
    $data["showAll"] = isset($_GET["showAll"]);
    $this->showView("zones/index", "Zones", $data);
  }
  
  private function runEdit($id) {
    $zoneDS = new \MyBind\DataStores\DnsZoneDataStore;
    $recordDS = new \MyBind\DataStores\DnsRecordDataStore;
    
    $zone = $zoneDS->getById($id);
    $records = $recordDS->getByZoneId($id);
    
    $data["zone"] = $zone;
    $data["records"] = $records;
    $this->showView("zones/edit", $zone->name, $data);
  }
}

?>
