<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

require_once "DataStore.php";
require_once "php/MyBind/Models/DnsRecord.php";

class DnsRecordDataStore extends DataStore {

  public function getByZoneId($zoneId) {
    $result = $this->query(
      "select id, name, type, ttl, aux, data ".
      "from mybindweb_dnsrecord ".
      "where zone_id = %d ".
      "order by type, name, aux, `data`",
      $zoneId);
    
    return $this->fromResult($result);
  }
  
  public function insert($record, $zoneId) {
    $this->query(
      "insert into mybindweb_dnsrecord " .
      "(zone_id, name, type, ttl, aux, data) values ".
      "(%d, %s, %s, %s, %s, %s)",
      $zoneId,
      $record->name,
      $record->type,
      $record->ttl,
      $record->aux,
      $record->data);
    
    return $this->sql->insert_id;
  }
  
  public function update($record, $zoneId) {
    $this->query(
      "update mybindweb_dnsrecord set " .
      "name = %s, type = %s, ttl = %s, aux = %s, data = %s ".
      "where id = %d and zone_id = %d",
      $record->name,
      $record->type,
      $record->ttl,
      $record->aux,
      $record->data,
      (int)$record->id,
      (int)$zoneId);
  }
  
  public function delete($id, $zoneId) {
    $this->query(
      "delete from mybindweb_dnsrecord ".
      "where id = %d and zone_id = %d",
      (int)$id,
      (int)$zoneId);
  }
  
  public function newModel() {
    return new \MyBind\Models\DnsRecord;
  }
}

?>
