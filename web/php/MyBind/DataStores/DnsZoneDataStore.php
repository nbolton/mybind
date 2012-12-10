<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

require_once "DataStore.php";
require_once "php/MyBind/Models/DnsZone.php";

class DnsZoneDataStore extends DataStore {

  public function getAll() {
    $result = $this->query(
      "select id, name, deleted, ".
      "sync_state as syncState, sync_msg as syncMessage ".
      "from mybindweb_dnszone ".
      "order by deleted, name");
    
    return $this->fromResult($result);
  }
  
  public function getByUserId($userId) {
    $result = $this->query(
      "select id, name, serial, deleted, ".
      "sync_state as syncState, sync_msg as syncMessage ".
      "from mybindweb_dnszone ".
      "where owner_id = %d ".
      "order by deleted, name",
      (int)$userId);
    
    return $this->fromResult($result);
  }
  
  public function getById($id) {
    $result = $this->query(
      "select id, owner_id as ownerId, name, ".
      "default_ttl as defaultTtl, serial, ".
      "sync_state as syncState, sync_msg as syncMessage ".
      "from mybindweb_dnszone ".
      "where id = %d",
      (int)$id);
    
    $zones = $this->fromResult($result);
    if (count($zones) == 0) {
      throw new \Exception("Zone does not exist: $id");
    }
    return $zones[0];
  }
  
  public function insert($zone, $ownerId) {
    $this->query(
      "insert into mybindweb_dnszone " .
      "(owner_id, name, default_ttl, serial, sync_cmd, sync_state) values ".
      "(%d, %s, %s, %s, %s, %s)",
      (int)$ownerId,
      $zone->name,
      $zone->defaultTtl,
      $zone->serial,
      $zone->syncCommand,
      $zone->syncState);
    
    return $this->sql->insert_id;
  }
  
  public function update($zone) {
    $this->query(
      "update mybindweb_dnszone set " .
      "name = %s, default_ttl = %s, serial = %s, renamed = %d, ".
      "sync_cmd = %s, sync_state = %s ".
      "where id = %d",
      $zone->name,
      $zone->defaultTtl,
      $zone->serial,
      (int)$zone->renamed,
      $zone->syncCommand,
      $zone->syncState,
      (int)$zone->id);
  }
  
  public function deleteSoft($id) {
    $this->query(
      "update mybindweb_dnszone ".
      "set deleted = 1, sync_state = 'SP', sync_cmd = %s ".
      "where id = %d",
      \MyBind\Models\SyncCommand::DeletePending,
      $id);
  }
  
  public function restore($id) {
    $this->query(
      "update mybindweb_dnszone ".
      "set deleted = 0, sync_state = 'SP', sync_cmd = %s ".
      "where id = %d",
      \MyBind\Models\SyncCommand::CreatePending,
      $id);
  }
  
  public function newModel() {
    return new \MyBind\Models\DnsZone;
  }
}

?>
