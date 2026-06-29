<?php
defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	// 'hostname' => 'anggarabw.com',
	// 'username' => 'anggarab_sim_sekolah',
	// 'password' => 'DZBYuM6q6gXtRPEUtW2L',
	// 'database' => 'anggarab_sim_sekolah',
	// 'hostname' => '109.111.53.58:12732',
	// 'username' => 'root',
	// 'password' => '123',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'sim_sekolah',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
