<?php
layout('base');
?>
<div class="py-5">
  <p>Welcome to <?= $fun['app.name'] ?>.</p>

  <?php if ($user = user()): ?>
    <p>You are logged in as <?= $user['name'] ?>. [<a href="<?= path('dashboard') ?>">DASHBOARD</a>]</p>
  <?php else: ?>
    <p>You have not <a href="<?= path('login') ?>">login</a> yet!</p>
  <?php endif ?>

  <div class="fs-5 my-3">Current env: <?= env() ?></div>
</div>
