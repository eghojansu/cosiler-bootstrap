<?php

guest();

$data = validate(array(
  'username' => 'trim|required',
  'password' => 'trim|required',
));

$found = $fun['db']->selectOne('user', array('userid = ?', $data['username']));

if (!$found || !password_verify($data['password'], $found['password'])) {
  errorCommit('Invalid credentials', null, $data);
  back();
}

if (!$found['active']) {
  errorCommit('Your account is inactive', null, $data);
  back();
}

userCommit($found['userid']);
messageCommit('Welcome back');
back();
