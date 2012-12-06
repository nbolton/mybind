<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<h2>Zones</h2>

<?php if (!$showAll): ?>
<p><a href="./?showAll">Show all</a></p>
<?php else: ?>
<p><a href="./">Show mine</a></p>
<?php endif ?>

<ul>
<?php foreach ($zones as $zone): ?>
  <li><a href="edit/<?=$zone->id?>/"><?=$zone->name?></a></li>
<?php endforeach ?>
</ul>
