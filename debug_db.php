<?php
define('BASEPATH', 'TRUE');
require_once('index.php');
$CI =& get_instance();
$res = $CI->db->query("SELECT id, name, telpon FROM siswa WHERE name LIKE '%Angelina%'")->result();
file_put_contents('debug_data.txt', print_r($res, true));
echo "Done";
