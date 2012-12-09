<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

require_once "DataStore.php";

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
  }
  
  public function update($record) {
    $this->query(
      "update mybindweb_dnsrecord set " .
      "name = %s, type = %s, ttl = %s, aux = %s, data = %s ".
      "where id = %d",
      $record->name,
      $record->type,
      $record->ttl,
      $record->aux,
      $record->data,
      (int)$record->id);
  }
  
  public function delete($id) {
    $this->query("delete from mybindweb_dnsrecord where id = %d", $id);
  }
}

?>
