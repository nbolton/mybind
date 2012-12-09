<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Models;

class DnsZone {
  public $id;
  public $ownerId;
  public $name;
  public $defaultTtl;
  public $syncState;
  public $syncCommand;
  public $syncMessage;
  
  public function getStatus() {
    switch ($this->syncState) {
      case "OK": return "Up to date"; break;
      case "SP": return "Synchronizing"; break;
      case "ER": return "Error: " . $this->syncMessage; break;
    }
  }
}

?>
