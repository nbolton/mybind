<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Models;

class SyncCommand {
  const NothingToDo = "OK";
  const UpdatePending = "UP";
  const CreatePending = "CP";
  const DeletePending = "DP";
}

class SyncState {
  const UpToDate = "OK";
  const SyncPending = "SP";
  const SyncActive = "SA";
  const SyncError = "SE";
}

class DnsZone {
  public $id;
  public $ownerId;
  public $name;
  public $defaultTtl;
  public $syncState;
  public $syncCommand;
  public $syncMessage;
  public $deleted;
  public $renamed;
  
  public function getStatus() {
    switch ($this->syncState) {
      case SyncState::UpToDate: return "Up to date"; break;
      case SyncState::SyncPending: return "Sync pending"; break;
      case SyncState::SyncActive: return "Sync active"; break;
      case SyncState::SyncError: return "Error: " . $this->syncMessage; break;
    }
  }
}

?>
