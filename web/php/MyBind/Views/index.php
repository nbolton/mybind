<?php

/*
* MyBind
* Copyright (C) 2012 Bolton Software Ltd.
* All rights reserved.
*/

?>

<?php if ($app->security->isLoggedIn()): ?>
<p><a href="zones/">Zones</a></p>
<?php else: ?>
<h2>Login</h2>
<form action="login/" method="post">
  <table style="width: 30%">
    <tr>
      <th>Email:</th>
      <td><input type="text" name="email" /></td>
    </tr>
    <tr>
      <th>Password:</th>
      <td><input type="password" name="password" /></td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: right"><input type="submit" /></td>
    </tr>
  </table>
</form>
<?php endif ?>
