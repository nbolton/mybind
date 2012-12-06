<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind\DataStores;

require_once "ConnectException.php";
require_once "QueryException.php";

abstract class DataStore {

  protected static $globalSql;
  protected $sql;
  
  public function __construct($sql = null) {
    if ($sql != null) {
      $this->sql = $sql;
    }
    else {
      if (self::$globalSql == null) {
        self::$globalSql = $this->connect();
      }
      $this->sql = self::$globalSql;
    }
  }
  
  protected function connect() {
    $s = \MyBind\App::$instance->settings;
    $sql = new \mysqli(
      $s["db"]["host"], $s["db"]["user"],
      $s["db"]["pass"], $s["db"]["name"]);
    
    if ($sql->connect_errno) {
      throw new ConnectException($sql->connect_error);
    }
    $sql->set_charset("utf8");
    return $sql;
  }
  
  public function query($format) {
    $args = $this->getCleanArgs(func_get_args());
    
    \MyBind\App::$instance->queryCount++;
    
    $query = count($args) != 0 ? vsprintf($format, $args) : $format;
    $result = $this->sql->query($query);
    
    if (!$result) {
      throw new QueryException($this->sql->error);
    }
    
    return $result;
  }
  
  public function multiQuery($format) {
    $args = $this->getCleanArgs(func_get_args());
    
    \MyBind\App::$instance->queryCount++;
    
    $query = count($args) != 0 ? vsprintf($format, $args) : $format;
    $this->sql->multi_query($query);
    
    $results = array();
    do {
      $this->sql->next_result();
      $result = $this->sql->store_result();
      if ($result == null) {
        throw new QueryException($this->sql->error);
      }
      array_push($results, $result);
    }
    while ($this->sql->more_results());
    
    return $results;
  }
  
  protected function getCleanArgs($funcArgs) {
    $args = array_slice($funcArgs, 1);
    foreach ($args as $k => $v) {
      $args[$k] = $this->cleanArg($v);
    }
    return $args;
  }
  
  public function cleanArg($v) {
    if ($v == null || $v == "") {
      return "NULL";
    }
    elseif (is_string($v)) {
      // escape any strings to prevent sql injection, and
      // also escape % for sprintf.
      return "\"" . $this->sql->escape_string(str_replace("%", "%%", $v)) . "\"";
    }
    elseif (is_object($v)) {
      return (string)$v->str;
    }
    else {
      return $v;
    }
  }
  
  protected function fromResult($result, $parser = null) {
    $data = array();
    if ($result == null || $result->num_rows == 0) {
      return $data;
    }
    
    while ($row = $result->fetch_object()) {
      array_push($data, $this->fromRow($row, $parser));
    }
    
    return $data;
  }
  
  protected function fromResultSingle($result) {
    if ($result == null || $result->num_rows == 0) {
      return null;
    }
    
    return $this->fromRow($result->fetch_object());
  }
  
  protected function fromRow($row, $parser = null) {
    $data = $this->newModel();
    foreach ($row as $k => $v) {
      $parsed = $this->parseField($k, $v);
      if ($parser != null) {
        $parsed = $parser($k, $v);
      }
      $data->$k = $parsed;
    }
    return $data;
  }
  
  protected function fromResultScalar($result) {
    if ($result == null || $result->num_rows == 0) {
      return null;
    }
    $row = $result->fetch_row();
    return $row[0];
  }
  
  protected function parseField($k, $v) {
    return $v;
  }
  
  protected function newModel() {
    return new \stdClass;
  }
  
  protected static function nullInt($int) {
    if ($int == null) {
      return null;
    }
    return (int)$int;
  }
  
  protected function format($format) {
    $args = $this->getCleanArgs(func_get_args());
    return vsprintf($format, $args);
  }
}

?>
