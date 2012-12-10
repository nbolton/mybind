<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind;

require_once "php/MyBind/DataStores/UserDataStore.php";

class Security {
  
  const SESSION_KEY = "userId";
  
  public $user;
  
  public function __construct($app) {
    $this->app = $app;
    $this->userDataStore = new \MyBind\DataStores\UserDataStore;
  }
  
  public function run() {
    $userId = $this->getUserId();
    if ($userId != null) {
      $this->user = $this->userDataStore->getById($userId);
    }
  }
  
  public function setUserId($id) {
    $_SESSION[self::SESSION_KEY] = $id;
  }
  
  public function logout() {
    // php bug #19586 can stop this from working on some machines.
    unset($_SESSION[self::SESSION_KEY]);
  }
  
  private function getUserId() {
    if (isset($_SESSION[self::SESSION_KEY]) && $_SESSION[self::SESSION_KEY] != null) {
      return $_SESSION[self::SESSION_KEY];
    }
    return null;
  }
  
  public function isLoggedIn() {
    return $this->user != null;
  }
}

?>
