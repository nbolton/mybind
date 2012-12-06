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
    <h1>MyBind</h1>
    <?php require_once "php/MyBind/Views/$page.php" ?>
    <p>Copyright &copy; 2012 <a href="http://boltonsoftware.com">Bolton Software Ltd.</a></p> 
  </body>
</html>
