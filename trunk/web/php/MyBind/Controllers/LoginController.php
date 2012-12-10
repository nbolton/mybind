<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\Controllers;

require_once "Controller.php";
require_once "php/MyBind/DataStores/UserDataStore.php";

class LoginController extends Controller {

  public function __construct() {
    $this->pathRegex = "/^login\/.*/";
  }
  
  public function run() {
    if (preg_match("/^login\/$/", $this->app->path)) {
      $this->runIndex();
    }
    else {
      throw new InvalidPathException($this->app->path);
    }
  }
  
  private function runIndex() {
    $ds = new \MyBind\DataStores\UserDataStore;
    $user = $ds->getByEmail($_POST["email"]);
    if ($user == null) {
      throw new \Exception("Invalid email address.");
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
}

?>
