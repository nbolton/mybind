<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

require_once "DataStore.php";

class DnsZoneDataStore extends DataStore {

  public function getAll() {
    $result = $this->query("select * from dnszone order by name");
    return $this->fromResult($result);
  }
  
  public function getById($id) {
    $result = $this->query("select * from dnszone where id = %d", $id);
    $zones = $this->fromResult($result);
    if (count($zones) == 0) {
      throw new \Exception("Zone does not exist: $id");
    }
    return $zones[0];
  }
}

?>
