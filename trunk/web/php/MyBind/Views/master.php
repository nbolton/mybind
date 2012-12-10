<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>
<html>
  <head>
    <title><?=$title?></title>
    <link rel="stylesheet" type="text/css" href="<?=$app->getFilePath("css/main.css")?>" />
    <?php if ($this->hasStyleFile()): ?>
    <link rel="stylesheet" type="text/css" href="<?=$this->getStyleFilePath()?>" />
    <?php endif ?>
    <script type="text/javascript" src="<?=$app->getFilePath("js/jquery-1.7.2.min.js")?>"></script>
    <script type="text/javascript" src="<?=$app->getFilePath("js/common.js")?>"></script>
    <?php if ($this->hasJavascriptFile()): ?>
    <script type="text/javascript" src="<?=$this->getJavascriptFilePath()?>"></script>
    <?php endif ?>
  </head>
  <body>
    <div class="header">
      <h1>MyBind</h1>
      <p class="tagline">If you know BIND, you know MyBind.</p>
      <p>
        <a href="<?=$app->getFilePath("")?>">Home</a>
        | <a href="<?=$app->getFilePath("about/")?>">About</a>
        | <a href="<?=$app->getFilePath("help/")?>">Help</a>
        <?php if ($app->security->isLoggedIn()): ?>
        | <a href="<?=$app->getFilePath("zones/")?>">Zones</a>
        | <a href="<?=$app->getFilePath("account/logout/")?>">Logout</a>
        <?php endif ?>
      </p>
    </div>
    <div class="content">
      <?php require_once "php/MyBind/Views/$view.php" ?>
    </div>
    <div class="footer">
      <p><a href="http://boltonsoftware.com">Bolton Software</a></p> 
    </div>
  </body>
</html>
