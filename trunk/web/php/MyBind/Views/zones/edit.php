<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<h2><?=$zone->name?></h2>
<p><a href="../../">Zones</a></p>

<form method="post">
  <table id="records" style="width: 100%">
    <tr>
      <th>Name</th>
      <th>TTL</th>
      <th>Type</th>
      <th></th>
      <th>Data</th>
      <th></th>
    </tr>
    <?php foreach ($records as $record): ?>
    <tr>
      <td style="width: 20%"><input type="text" name="name" value="<?=$record->name?>" /></td>
      <td style="width: 5%"><input type="text" name="ttl" value="<?=$record->ttl?>" /></td>
      <td style="width: 10%"><input type="text" name="type" value="<?=$record->type?>" class="type"/></td>
      <td style="width: 5%"><input type="text" name="aux" value="<?=$record->aux?>" class="aux" /></td>
      <td><input type="text" name="data" value="<?=$record->data?>" /></td>
      <td style="width: 1px">
        <a href="javascript:void(0)" class="delete">X</a>
        <input type="hidden" name="delete" />
      </td>
    </tr>
    <?php endforeach ?>
  </table>
  
  <input type="submit" value="Save" />
  
  <p><a href="javascript:void(0)" class="add">Add record</a></p>
  
</form>
