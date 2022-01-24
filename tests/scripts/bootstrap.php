<?php

define('COSILER_ENV', 'test');
define('COSILER_TMP', dirname(dirname(__DIR__)) . '/var/tests');

is_dir(COSILER_TMP) || mkdir(COSILER_TMP, 0777, true);
