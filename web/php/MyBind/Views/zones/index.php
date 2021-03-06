<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<h2>Zones</h2>

<p><a href="new/">New zone</a></p>

<?php if ($app->security->user->isAdmin): ?>
<?php if (!$showAll): ?>
<p><a href="./?showAll">Show all</a></p>
<?php else: ?>
<p><a href="./">Show mine</a></p>
<?php endif ?>
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
    <td><?=$zone->getStatus()?></td>
    <td><a href="edit/<?=$zone->id?>/">Edit</a></td>
    <?php if ($zone->deleted): ?>
    <td><a href="restore/<?=$zone->id?>/">Restore</a></td>
    <?php else: ?>
    <td><a href="delete/<?=$zone->id?>/">Delete</a></td>
    <?php endif ?>
  </tr>
  <?php endforeach ?>
</table>
