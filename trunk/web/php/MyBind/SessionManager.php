<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind;

class SessionManager {

  var $lifetime;

  function __construct($settings) {
    $this->settings = $settings;
    $this->lifetime = $settings["session"]["lifetime"];
    
    session_set_save_handler( 
      array(&$this, "open"),
      array(&$this, "close"),
      array(&$this, "read"),
      array(&$this, "write"),
      array(&$this, "destroy"),
      array(&$this, "gc")
    );
    
    session_set_cookie_params($this->lifetime);
  }
  
  public function start() {
    session_start();
  }
  
  public function getMysql() {
    $s = $this->settings;
    return new \mysqli(
      $s["db"]["host"], $s["db"]["user"],
      $s["db"]["pass"], $s["db"]["name"]);
  }

  function open($save_path, $session_name) {
    return true;
  }

  function close() {
    return true;
  }

  function read($id) {    
    $mysql = $this->getMysql();
    $result = $mysql->query(sprintf(
      "select data from session where ".
      "id = '%s' and expires > %d",
      $mysql->escape_string($id),
      time()
    ));
    if ($result == null) {
      throw new \Exception($mysql->error);
    }
    
    $data = "";
    if ($result->num_rows != 0) {
      $row = $result->fetch_assoc();
      $data = $row["data"];
    }
    return $data;
  }

  function write($id, $data) {
    $mysql = $this->getMysql();
    if ($data != "") {
      $result = $mysql->query(sprintf(
        "replace session (id, data, expires) values ('%s', '%s', %d)",
        $mysql->escape_string($id),
        $mysql->escape_string($data),
        (time() + $this->lifetime)
      ));
      if ($result == null) {
        throw new Exception($mysql->error);
      }
    }
    else {
      // delete sessions with no data.
      $this->destroy($id);
    }
    return true;
  }

  function destroy($id) {
    $mysql = $this->getMysql();
    $result = $mysql->query(sprintf(
      "delete from session where id = '%s'",
      $mysql->escape_string($id)
    ));
    if ($result == null) {
      throw new Exception($mysql->error);
    }
    return true;
  }

  function gc() {
    $mysql = $this->getMysql();
    $result = $mysql->query(
      "delete from session where expires < unix_timestamp()"
    );
    if ($result == null) {
      throw new Exception($mysql->error);
    }
    return true;
  }
}

?>
