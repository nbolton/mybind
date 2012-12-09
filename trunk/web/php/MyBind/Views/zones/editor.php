<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<?php if (isset($zone->name)): ?>
<h2><?=$zone->name?></h2>
<?php endif ?>

<p><a href="<?=$app->getFilePath("zones/")?>">Zones</a></p>

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
    <?php $i = 0 ?>
    <?php foreach ($records as $record): ?>
    <?php $i++ ?>
    <tr>
      <td style="width: 20%"><input type="text" name="r<?=$i?>[name]" value="<?=$record->name?>" /></td>
      <td style="width: 5%"><input type="text" name="r<?=$i?>[ttl]" value="<?=$record->ttl?>" /></td>
      <td style="width: 10%"><input type="text" name="r<?=$i?>[type]" value="<?=$record->type?>" class="type"/></td>
      <td style="width: 5%"><input type="text" name="r<?=$i?>[aux]" value="<?=$record->aux?>" class="aux" /></td>
      <td><input type="text" name="r<?=$i?>[data]" value="<?=$record->data?>" /></td>
      <td style="width: 1px">
        <a href="javascript:void(0)" class="delete">X</a>
        <input type="hidden" name="r<?=$i?>[delete]" />
        <input type="hidden" name="r<?=$i?>[number]" value="<?=$i?>" />
      </td>
    </tr>
    <?php endforeach ?>
  </table>
  
  <p><a href="javascript:void(0)" class="add">Add record</a></p>
  <p><input type="submit" value="Save" /></p>
  
</form>
