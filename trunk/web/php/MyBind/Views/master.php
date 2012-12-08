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
  </head>
  <body>
    <div class="header">
      <h1>MyBind</h1>
    </div>
    <div class="content">
      <?php require_once "php/MyBind/Views/$page.php" ?>
    </div>
    <div class="footer">
      <p><a href="http://boltonsoftware.com">Bolton Software</a></p> 
    </div>
  </body>
</html>
