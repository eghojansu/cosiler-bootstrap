<?php

guest();

$data = validate(array(
  'username' => 'trim|required',
  'password' => 'trim|required',
));

$found = $fun['db']->selectOne('user', array('userid = ?', $data['username']));

if (!$found || !password_verify($data['password'], $found['password'])) {
  error_commit('Invalid credentials', null, $data);
  back();
}

if (!$found['active']) {
  error_commit('Your account is inactive', null, $data);
  back();
}

user_commit($found['userid']);
message_commit('Welcome back');
record('login');
redirect('/dashboard');
