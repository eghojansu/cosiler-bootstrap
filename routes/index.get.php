<div class="py-5">
  <p>Welcome to <?= $fun['app.name'] ?>.</p>

  <?php if ($user = user()): ?>
    <p>You are logged in as <?= $user['name'] ?>. [<a onclick="return confirm('Are you sure to LOGOUT?')" href="<?= path('logout') ?>">LOGOUT</a>]</p>
  <?php else: ?>
    <p>You have not <a href="<?= path('login') ?>">login</a> yet!</p>
  <?php endif ?>
</div>
