<?php

guard('admin');
not_found_if(!($user = get_one('user', $criteria = array('userid <> ? and userid = ?', user_id(), $params['user']))));

delete('user', $criteria);
redirect_deleted('user');
