<?php

not_found_if(is_file($versionFile = $fun['tmp_dir'] . '/version.txt'));

if (!is_writable(dirname($versionFile))) {
  errorCommit('Temp directory is not writable');
  back();
}

/** @var PDO */
$pdo = $fun['db']->getPdo();

foreach (glob($fun['project_dir'] . '/databases/'. $fun['connection.default'] . '/*.sql') as $file) {
  $pdo->exec(file_get_contents($file));
}

$fun['db']->update('user', array('active' => 1, 'password' => password_hash('admin123', PASSWORD_BCRYPT)), array('userid = "admin"'));

file_put_contents($versionFile, 'Installed at ' . date('Y-m-d H:i:s'));
redirect('/');
