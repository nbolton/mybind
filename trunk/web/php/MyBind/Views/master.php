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
    <script type="text/javascript" src="<?=$app->getFilePath("js/jquery-1.7.2.min.js")?>"></script>
    <script type="text/javascript" src="<?=$app->getFilePath("js/common.js")?>"></script>
    <?php if ($this->hasJavascriptFile()): ?>
    <script type="text/javascript" src="<?=$this->getJavascriptFilePath()?>"></script>
    <?php endif ?>
  </head>
  <body>
    <div class="header">
      <h1>MyBind</h1>
    </div>
    <div class="content">
      <?php require_once "php/MyBind/Views/$view.php" ?>
    </div>
    <div class="footer">
      <p><a href="http://boltonsoftware.com">Bolton Software</a></p> 
    </div>
  </body>
</html>
