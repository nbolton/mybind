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
      $this->handleEditorPost($zoneDS, $recordDS, $mode, $id);
      return;
    }
    
    switch ($mode) {
      case EditorMode::Create:
        $zone = new \MyBind\Models\DnsZone;
        $zone->defaultTtl = "1h";
        
        $record = new \MyBind\Models\DnsRecord;
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
  
  private function handleEditorPost($zoneDS, $recordDS, $mode, $id) {
    switch ($mode) {
      case EditorMode::Create:
        $zone = new \MyBind\Models\DnsZone;        
        $this->setFormValues($zone);
        
        // use RFC style serial number (can make DNS troubleshooting a bit easier).
        $zone->serial = date("Ymd00");
        
        $zone->syncCommand = \MyBind\Models\SyncCommand::CreatePending;
        $zone->syncState = \MyBind\Models\SyncState::SyncPending;
        
        $id = $zoneDS->insert($zone, 1);
        
        // TODO: make sure only insert is allowed.
        $this->saveRecordChanges($recordDS, $id);
        break;
      
      case EditorMode::Update:
        $zone = $zoneDS->getById($id);
        $this->setFormValues($zone);
        
        // if serial was last set today, make sure we increment the rev digits.
        $serialDate = date("Ymd");
        $rev = 0;
        if (substr($zone->serial, 0, 8) == $serialDate) {
          $rev = (int)substr($zone->serial, 8) + 1;
        }
        $zone->serial = $serialDate . str_pad($rev, 2, "0", STR_PAD_LEFT);
        
        $zone->syncCommand = \MyBind\Models\SyncCommand::UpdatePending;
        $zone->syncState = \MyBind\Models\SyncState::SyncPending;
        
        // TODO: make sure the user owns this zone.
        $zoneDS->update($zone);
        
        // TODO: make sure the user owns the records.
        $this->saveRecordChanges($recordDS, $id);
        break;
    }
    
    header("Location: " . $this->app->getFilePath("zones/edit/$id/"));
  }
  
  private function setFormValues($zone) {
    $zone->name = $_POST["zone"]["name"];
    $zone->defaultTtl = $_POST["zone"]["defaultTtl"];
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
        
        default:
          throw new \Exception("Invalid record save action: " . $record->action);
      }
    }
  }
  
  private function getRecordObject($fields) {
    $record = new \MyBind\Models\DnsRecord;
    foreach ($fields as $k => $v) {
      // only update fields that actually exist.
      $record->$k = $v;
    }
    return $record;
  }
}

?>
