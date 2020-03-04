<?php
$cfg = array();
$cfg['db_engine'] = 'SQLite';
$cfg['db_version'] = '5.0';
$cfg['db_login'] = 'testpluf';
$cfg['db_password'] = 'testpluf';
$cfg['db_server'] = 'localhost';
$cfg['db_database'] = dirname(__FILE__) . '/../tmp/dp.test.sqlite.db';
$cfg['db_table_prefix'] = 'px_test_' . rand();
return $cfg;

