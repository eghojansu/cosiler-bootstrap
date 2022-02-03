<?php
guard();
$user = user();
$message = message();
?>
<div class="py-5">
  <?= alert($message, 'info') ?>

  <p>Welcome to <?= $fun['app.name'] ?>.</p>
  <p>You are logged in as <?= $user['name'] ?>.</p>
</div>
