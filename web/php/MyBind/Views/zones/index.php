<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<h2>Zones</h2>

<ul>
<?php foreach($zones as $zone): ?>
  <li><a href="edit/<?=$zone->id?>/"><?=$zone->name?></a></li>
<?php endforeach ?>
</ul>
