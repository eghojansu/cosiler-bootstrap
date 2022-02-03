<?php

guard();

$data = validate(array(
  'userid' => 'trim|required|max:8|unique:user',
  'name' => 'trim|required|max:32',
  'email' => 'trim|required|email|max:60',
  'roles' => 'required|array|in:' . implode(',', $fun['choice.roles']),
  'active' => 'trim|required|in:' . implode(',', $fun['choice.active']),
  'password' => 'trim|required|min:6',
));
$data['password'] = user_password($data['password']);
$data['roles'] = implode(',', $data['roles']);

save('user', $data);
redirect_saved('user');
