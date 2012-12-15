<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

namespace MyBind;

require_once "Security.php";
require_once "SessionManager.php";
require_once "SecurityException.php";
require_once "Controllers/ControllerProvider.php";

class App {

  public static $instance;
  public $errorShown = false;

  public function __construct() {
    self::$instance = $this;
    
    $this->settings = parse_ini_file("settings.ini", true);
    
    if ($this->isErrorHandlingEnabled()) {
      set_error_handler(array($this, "handleError"));
    }
    
    $this->path = isset($_GET["path"]) ? $_GET["path"] : "";
    $this->controllerProvider = new Controllers\ControllerProvider($this);
    $this->security = new Security($this);
    $this->sessionManager = new SessionManager($this->settings);
  }

  public function run() {
    try {
      $this->sessionManager->start();
      $this->security->run();
      
      $controller = $this->controllerProvider->getForPath();
      $controller->run();
    }
    catch (Controllers\InvalidPathException $ex) {
      $this->showErrorPage(404);
    }
    catch (SecurityException $ex) {
      $this->showErrorPage(403);
    }
    catch (\Exception $ex) {
      if ($this->isErrorHandlingEnabled()) {
        $this->showErrorPage(500);
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
  
  public function handleError($code, $message, $file, $line) {
    $this->showErrorPage(500);
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
  
  private function isErrorHandlingEnabled() {
    return $this->settings["error"]["handle"];
  }
  
  private function showErrorPage($code) {
    if ($this->errorShown) {
      return;
    }
    
    switch ($code) {
      case 403: $m = "Forbidden"; break;
      case 404: $m = "Not Found"; break;
      case 500: $m = "Server Error"; break;
      default: $m = "Error"; break;
    }
    
    $home = $this->getFilePath("");
    
    header("HTTP/1.0 $code $m");
    echo "<html><body><h1>$code: $m</h1><p><a href=\"$home\">Home</a></p></body></html>";
    $this->errorShown = true;
  }
  
  private function sendErrorReport($message) {
    $to = $this->settings["error"]["to"];
    $from = $this->settings["error"]["from"];
    $request = var_export($_REQUEST, true);
    $server = var_export($_SERVER, true);
    mail($to, "Error", "$message\n\n$request\n\n$server", "From: $from");
  }
}

?>
