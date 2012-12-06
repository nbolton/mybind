<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<h2><?=$zone->name?></h2>
<p><a href="../../">Zones</a></p>

<form>
  <table>
    <tr>
      <th>Name</th>
      <th>TTL</th>
      <th>Type</th>
      <th>Aux</th>
      <th>Data</th>
    </tr>
    <?php foreach ($records as $record): ?>
    <tr>
      <td><input type="text" value="<?=$record->name?>" /></td>
      <td><input type="text" value="<?=$record->ttl?>" /></td>
      <td><input type="text" value="<?=$record->type?>" /></td>
      <td><input type="text" value="<?=$record->aux?>" /></td>
      <td><input type="text" value="<?=$record->data?>" /></td>
    </tr>
    <?php endforeach ?>
  </table>
</form>
