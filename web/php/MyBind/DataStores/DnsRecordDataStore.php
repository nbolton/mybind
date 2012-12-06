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
      "select * from dnsrecord ".
      "where zoneId = %d ".
      "order by type, name, aux, `data`",
      $zoneId);
    return $this->fromResult($result);
  }
}

?>
