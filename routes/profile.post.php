<?php

guard();

$data = validate(array(
  'name' => 'trim|required|max:32',
  'email' => 'trim|required|email|max:60',
  'password' => 'trim|nullable|min:6',
));

if (empty($data['password'])) {
  unset($data['password']);
} else {
  $data['password'] = user_password($data['password']);
}

save('user', $data, array('userid = ?', user_id()));
redirect_saved('profile');
