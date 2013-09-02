<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";
require_once "php/MyBind/DataStores/UserDataStore.php";

class AccountController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^account\/.*/";
  }
  
  public function run() {
    if (preg_match("/^account\/login\/$/", $this->app->path)) {
      $this->runLogin();
    }
    else if (preg_match("/^account\/logout\/$/", $this->app->path)) {
      $this->runLogout();
    }
    else {
      throw new InvalidPathException($this->app->path);
    }
  }
  
  private function runLogin() {
    try {
      if (!isset($_POST["email"]) || ($_POST["email"] == "")) {
        throw new \Exception("No email address provided.");
      }
      
      $ds = new \MyBind\DataStores\UserDataStore;
      $user = $ds->getByEmail($_POST["email"]);
      if ($user == null) {
        throw new \Exception("Invalid email address.");
      }
      
      if ($user->password == "") {
        throw new \Exception("User has no password, email=" . $user->email);
      }
      
      $passParts = preg_split("/[$]/", $user->password);
      $salt = $passParts[1];
      $existingHash = $passParts[2];
      $attemptHash = sha1($salt . $_POST["password"]);
      
      if ($attemptHash != $existingHash) {
        throw new \Exception("Invalid password.");
      }
      
      $this->app->security->setUserId($user->id);
      header("Location: " . $this->app->getFilePath(""));
    }
    catch (\Exception $ex) {
      die($ex->getMessage());
    }
  }
  
  private function runLogout() {
    $this->app->security->logout();
    header("Location: " . $this->app->getFilePath(""));
  }
}

?>
