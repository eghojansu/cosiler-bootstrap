<?php

guard('admin');
menu_active('user');

$data = data();
$error = error();
?>
<h4 class="border-bottom p-3">New User</h4>

<?= alert($error['message'] ?? null, 'danger') ?>

<form method="post" style="max-width: 500px">
  <div class="row">
    <label for="inputUserid" class="col-sm-3 col-form-label">Username</label>
    <div class="col-sm-9">
      <?= input('userid', $data['userid'] ?? null, array('required', 'placeholder' => 'Username', 'autofocus'), array('form-control', isset($error['errors']['userid']) ? 'is-invalid' : null)) ?>
      <?= feedback($error['errors']['userid'] ?? null) ?>
    </div>
  </div>
  <div class="row mt-3">
    <label for="inputName" class="col-sm-3 col-form-label">Name</label>
    <div class="col-sm-9">
      <?= input('name', $data['name'] ?? null, array('required'), array('form-control', isset($error['errors']['name']) ? 'is-invalid' : null)) ?>
      <?= feedback($error['errors']['name'] ?? null) ?>
    </div>
  </div>
  <div class="row mt-3">
    <label for="inputEmail" class="col-sm-3 col-form-label">Email</label>
    <div class="col-sm-9">
      <?= input('email', $data['email'] ?? null, array('required'), array('form-control', isset($error['errors']['email']) ? 'is-invalid' : null), 'email') ?>
      <?= feedback($error['errors']['email'] ?? null) ?>
    </div>
  </div>
  <div class="row mt-3">
    <label for="inputRoles" class="col-sm-3 col-form-label">Role</label>
    <div class="col-sm-9">
      <?= choice('roles', $fun['choice.roles'], $data['roles'] ?? null, null, array(isset($error['errors']['roles']) ? 'is-invalid' : null), true) ?>
      <?= feedback($error['errors']['roles'] ?? null) ?>
    </div>
  </div>
  <div class="row mt-3">
    <label for="inputActive" class="col-sm-3 col-form-label">Status</label>
    <div class="col-sm-9">
      <?= choice('active', $fun['choice.active'], $data['active'] ?? null, null, array(isset($error['errors']['active']) ? 'is-invalid' : null), true) ?>
      <?= feedback($error['errors']['active'] ?? null) ?>
    </div>
  </div>
  <div class="row mt-3">
    <label for="inputPassword" class="col-sm-3 col-form-label">Password</label>
    <div class="col-sm-9">
      <?= input('password', null, array('data-generate' => 'password'), array('form-control', isset($error['errors']['password']) ? 'is-invalid' : null), 'password') ?>
      <?= feedback($error['errors']['password'] ?? null) ?>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-sm-9 offset-sm-3">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="<?= path('/user') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </div>
</form>
