<?php

guest();

$data = data();
$error = error();
?>
<div class="mx-auto my-5" style="max-width: 280px">
  <form method="post" autocomplete="off">
    <h1 class="h3 mb-3 fw-normal text-center">Please sign in</h1>
    <?= alert($error['message'] ?? null, 'danger', false) ?>
    <div class="form-floating">
      <?= input('username', $data['username'] ?? null, null, array('form-control', isset($error['errors']['username']) ? 'is-invalid' : null)) ?>
      <label for="inputUsername">Username</label>
      <?= feedback($error['errors']['username'] ?? null) ?>
    </div>
    <div class="form-floating mt-3">
      <?= input('password', null, null, array('form-control', isset($error['errors']['password']) ? 'is-invalid' : null), 'password') ?>
      <label for="inputPassword">Password</label>
      <?= feedback($error['errors']['password'] ?? null) ?>
    </div>

    <button class="w-100 btn btn-lg btn-primary mt-3" type="submit">Sign in</button>
    <a href="<?= path('/') ?>" class="w-100 btn btn-secondary mt-2">Cancel</a>
    <p class="mt-5 mb-3 text-muted text-center">&copy; <?= $fun['app.alias'] ?> &ndash; <?= $fun['app.year'] ?></p>
  </form>
</div>
