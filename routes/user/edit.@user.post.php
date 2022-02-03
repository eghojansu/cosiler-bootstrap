<?php

guard();
not_found_if(!($user = get_one('user', $criteria = array('userid <> ? and userid = ?', user_id(), $params['user']))));

$data = validate(array(
  'name' => 'trim|required|max:32',
  'email' => 'trim|required|email|max:60',
  'roles' => 'required|array|in:' . implode(',', $fun['choice.roles']),
  'active' => 'trim|required|in:' . implode(',', $fun['choice.active']),
  'password' => 'trim|nullable|min:6',
));

if (empty($data['password'])) {
  unset($data['password']);
} else {
  $data['password'] = user_password($data['password']);
}

$data['roles'] = implode(',', $data['roles']);

save('user', $data, $criteria);
redirect_saved('user');
