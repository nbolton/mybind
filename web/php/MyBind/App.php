<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind;

require_once "Controllers/ControllerProvider.php";

class App {

  public static $instance;
  public $errorShown = false;

  public function __construct() {
    $this->path = isset($_GET["path"]) ? $_GET["path"] : "";
    $this->settings = parse_ini_file("settings.ini", true);
    $this->controllerProvider = new Controllers\ControllerProvider($this);
    self::$instance = $this;
    
    if ($this->isErrorHandlingEnabled()) {
      set_error_handler(array($this, "handleError"));
    }
  }

  public function run() {
    try {
      $controller = $this->getController();
      $controller->run();
    }
    catch (\Exception $ex) {
      if ($this->isErrorHandlingEnabled()) {
        $this->showErrorPage();
        $this->sendErrorReport($ex);
      }
      throw $ex;
    }
  }
  
  public function getFilePath($filename) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $lastSlash = strrpos($scriptName, "/");
    $root = substr($scriptName, 0, $lastSlash);
    return "$root/$filename";
  }
  
  private function getController() {
    try {
      return $this->controllerProvider->getForPath();
    }
    catch (Controllers\InvalidPathException $ex) {
      header("HTTP/1.0 404 Not Found");
      echo "<html><body><h1>404: Not Found</h1></body></html>";
      exit;
    }
  }
  
  private function isErrorHandlingEnabled() {
    return $this->settings["error"]["handle"];
  }
  
  public function handleError($code, $message, $file, $line) {
    $this->showErrorPage();
    switch ($code) {
      case E_WARNING: $codeString = "E_WARNING"; break;
      case E_NOTICE: $codeString = "E_NOTICE"; break;
      case E_USER_ERROR: $codeString = "E_USER_ERROR"; break;
      case E_USER_WARNING: $codeString = "E_USER_WARNING"; break;
      case E_USER_NOTICE: $codeString = "E_USER_NOTICE"; break;
      default: $codeString = $code; break;
    }
    $this->sendErrorReport("$file:$line\n$codeString - $message");
    exit;
  }
  
  private function showErrorPage() {
    if ($this->errorShown) {
      return;
    }
    header("HTTP/1.0 500 Server Error");
    echo "<html><body><h1>500: Server Error</h1></body></html>";
    $this->errorShown = true;
  }
  
  private function sendErrorReport($message) {
    $to = $this->settings["error"]["to"];
    $from = $this->settings["error"]["from"];
    mail($to, "Error", $message, "From: $from");
  }
}

?>
