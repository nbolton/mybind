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
  <table style="width: 100%">
    <tr>
      <th>Name</th>
      <th>TTL</th>
      <th>Type</th>
      <th>Aux</th>
      <th>Data</th>
    </tr>
    <?php foreach ($records as $record): ?>
    <tr>
      <td style="width: 20%"><input type="text" value="<?=$record->name?>" /></td>
      <td style="width: 5%"><input type="text" value="<?=$record->ttl?>" /></td>
      <td style="width: 10%"><input type="text" value="<?=$record->type?>" /></td>
      <td style="width: 5%"><input type="text" value="<?=$record->aux?>" /></td>
      <td><input type="text" value="<?=$record->data?>" /></td>
    </tr>
    <?php endforeach ?>
  </table>
  
  <p><a href="javascript:void(0)">Add record</a></p>
  
</form>
