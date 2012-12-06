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
}

?>
