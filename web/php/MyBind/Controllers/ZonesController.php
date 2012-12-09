<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";
require_once "EditorMode.php";
require_once "php/MyBind/DataStores/DnsZoneDataStore.php";
require_once "php/MyBind/DataStores/DnsRecordDataStore.php";

class ZonesController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^zones\/.*/";
  }
  
  public function run() {
    if (preg_match("/^zones\/edit\/(\d+)\/$/", $this->app->path, $m)) {
      $this->runEditor(EditorMode::Update, (int)$m[1]);
    }
    else if (preg_match("/^zones\/new\//", $this->app->path, $m)) {
      $this->runEditor(EditorMode::Create);
    }
    else if (preg_match("/^zones\/$/", $this->app->path)) {
      $this->runIndex();
    }
    else {
      throw new InvalidPathException($this->app->path);
    }
  }
  
  private function runIndex() {
    $ds = new \MyBind\DataStores\DnsZoneDataStore;
    
    if (!isset($_GET["showAll"])) {
      $data["zones"] = $ds->getByUserId(1);
    }
    else {
      $data["zones"] = $ds->getAll();
    }
    
    $data["showAll"] = isset($_GET["showAll"]);
    $this->showView("zones/index", "Zones", $data);
  }
  
  private function runEditor($mode, $id=null) {
    $zoneDS = new \MyBind\DataStores\DnsZoneDataStore;
    $recordDS = new \MyBind\DataStores\DnsRecordDataStore;
    
    if ($this->isPost()) {
      
      switch ($mode) {
        case EditorMode::Create:
          $zone = new \stdClass;
          $this->applyFormValues($zone);
          $zoneDS->insert($zone);
          break;
        
        case EditorMode::Update:
          $zone = $zoneDS->getById($id);
          $this->applyFormValues($zone);
          $this->update($zone);
          break;
      }
      
      header("Location: " . $this->app->getFilePath("zones/"));
      return;
    }
    
    switch ($mode) {
      case EditorMode::Create:
        $zone = new \stdClass;
        $records = array();
        $title = "New";
        break;
      
      case EditorMode::Update:
        $zone = $zoneDS->getById($id);
        $records = $recordDS->getByZoneId($id);
        $title = $zone->name;
        break;
    }
    
    $data["zone"] = $zone;
    $data["records"] = $records;
    $this->showView("zones/editor", $title, $data);
  }
}

?>
