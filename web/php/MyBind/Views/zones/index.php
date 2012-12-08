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

<table id="zones">
  <tr>
    <th>Zone</th>
    <th>Status</th>
    <th></th>
    <th></th>
  </tr>
  <?php foreach ($zones as $zone): ?>
  <tr>
    <td><?=$zone->name?></td>
    <td>OK</td>
    <td></td>
    <td><a href="edit/<?=$zone->id?>/">Edit</a></td>
  </tr>
  <?php endforeach ?>
</table>
