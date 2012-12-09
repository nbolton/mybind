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

class SyncCommand {
  const Update = "UP";
}

class SyncState {
  const Pending = "SP";
}

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
          break;
        
        case EditorMode::Update:
          $zone = $zoneDS->getById($id);
          
          $zone->syncCommand = SyncCommand::Update;
          $zone->syncState = SyncState::Pending;
          
          // TODO: make sure the user owns this zone.
          $zoneDS->update($zone);
          
          // TODO: make sure the user owns the records.
          $this->saveRecordChanges($recordDS, $id);
          break;
      }
      
      header("Location: " . $this->app->getFilePath("zones/edit/$id/"));
      return;
    }
    
    switch ($mode) {
      case EditorMode::Create:
        $zone = new \stdClass;
        $record = new \stdClass;
        $record->id = null;
        $record->name = null;
        $record->ttl = null;
        $record->type = null;
        $record->aux = null;
        $record->data = null;
        $records = array();
        array_push($records, $record);
        $title = "New";
        $defaultRecordAction = "insert";
        break;
      
      case EditorMode::Update:
        $zone = $zoneDS->getById($id);
        $records = $recordDS->getByZoneId($id);
        $title = $zone->name;
        $defaultRecordAction = "update";
        break;
    }
    
    $data["zone"] = $zone;
    $data["records"] = $records;
    $data["defaultRecordAction"] = $defaultRecordAction;
    $data["mode"] = $mode;
    $this->showView("zones/editor", $title, $data);
  }
  
  private function saveRecordChanges($recordDS, $zoneId) {
    $recordCount = (int)$_POST["recordCount"];
    for ($i = 0; $i < $recordCount; $i++) {
      $record = $this->getRecordObject($_POST["r" . $i]);
      
      switch ($record->action) {
        case "insert":
          // TODO: make sure user owns this zone
          $recordDS->insert($record, $zoneId);
          break;
        
        case "update":
          // TODO: make sure user owns this zone/record
          $recordDS->update($record);
          break;
        
        case "delete":
          // TODO: make sure user owns this zone/record
          $recordDS->delete($record->id);
          break;
        
        throw new \Exception("Invalid record save action: " . $record->action);
      }
    }
  }
  
  private function getRecordObject($fields) {
    $record = new \stdClass;
    foreach ($fields as $k => $v) {
      $record->$k = $v;
    }
    return $record;
  }
}

?>
