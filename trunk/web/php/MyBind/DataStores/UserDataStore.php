<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

require_once "DataStore.php";

class UserDataStore extends DataStore {

  public function getById($id) {
    $result = $this->query(
      "select id, is_superuser as isAdmin ".
      "from auth_user ".
      "where id = %d",
      (int)$id);
    
    $users = $this->fromResult($result);
    if (count($users) == 0) {
      throw new \Exception("User does not exist with ID: $id");
    }
    return $users[0];
  }

  public function getByEmail($email) {
    $result = $this->query(
      "select id, password ".
      "from auth_user ".
      "where username = %s",
      $email);
    
    $users = $this->fromResult($result);
    if (count($users) == 0) {
      throw new \Exception("User does not exist by email: $email");
    }
    return $users[0];
  }
}

?>
